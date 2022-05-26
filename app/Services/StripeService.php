<?php

namespace App\Services;

use App\Exceptions\ApiValidationException;
use App\Http\Responses\ApiErrorResponse;
use App\Models\StripeUser;
use App\Models\User;
use App\Models\UserCard;
use App\Models\UserPlan;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;
use Cartalyst\Stripe\Stripe;

class StripeService
{
    /**
     * @var \Stripe\StripeClient
     */
    private \Stripe\StripeClient $stripe_client;

    /**
     * StripeService constructor.
     */
    public function __construct()
    {
        $this->stripe_client = new \Stripe\StripeClient(
            config('services.stripe.secret_key')
        );
    }

    /**
     * @param array $data
     * @param User $user
     * @return mixed
     * @throws ApiValidationException
     */
    public function createPaymentMethod(array $data, User $user)
    {
        try {
            $this->initStripe();
            $token = $data['token_id'];
            $user->load('stripe_user');
            if ($user->stripe_user) {
                $card = $this->stripe_client->customers->createSource(
                    $user->stripe_user->customer_id,
                    ['source' => $token]
                );

                $user_card = UserCard::where('user_id', $user->id)
                    ->updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'user_id' => $user->id,
                            'customer' => $card['customer'],
                            'card' => $card['id'],
                            'last4' => $card['last4'],
                            'exp_year' => $card['exp_year'],
                            'exp_month' => $card['exp_month'],
                            'brand' => $card['brand'],
                            'country' => $card['country'],
                            'address_zip' => $card['address_zip']
                        ]);
            } else {
                $customer = \Stripe\Customer::create([
                    'source' => $token,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                ]);
                $card = $this->getInfoCard($customer['id'], $customer['default_source']);
                StripeUser::create(['user_id' => $user->id, 'customer_id' => $customer['id']]);
                $user_card = UserCard::create([
                    'user_id' => $user->id,
                    'customer' => $card['customer'],
                    'card' => $card['id'],
                    'last4' => $card['last4'],
                    'exp_year' => $card['exp_year'],
                    'exp_month' => $card['exp_month'],
                    'brand' => $card['brand'],
                    'country' => $card['country'],
                    'address_zip' => $card['address_zip']
                ]);

            }
        } catch (\Exception $e) {
            throw new ApiValidationException($e->getMessage());
        }

        return $user_card;
    }

    /**
     * @param $customer_id
     * @param $card
     * @return \Stripe\AlipayAccount|\Stripe\BankAccount|\Stripe\BitcoinReceiver|\Stripe\Card|\Stripe\Source
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getInfoCard($customer_id, $card)
    {
        return $this->stripe_client->customers->retrieveSource(
            $customer_id,
            $card,
        );
    }

    /**
     * @param $payment_intent_id
     * @param $user
     * @return \Stripe\PaymentIntent
     * @throws ApiValidationException
     */
    public function confirmPaymentIntents($payment_intent_id, $user): \Stripe\PaymentIntent
    {
        try {
            return $this->stripe_client->paymentIntents->confirm(
                $payment_intent_id,
                ['payment_method' => $user->user_card->card]
            );
        } catch (\Exception $exception) {
            Log::info($exception);
            throw new ApiValidationException('Could not process payment. Please contact your bank and try again.');
        }
    }

    /**
     * @param $user
     * @param $amount
     * @param null $description
     * @return \Stripe\PaymentIntent
     * @throws ApiValidationException
     */
    public function paymentIntents($user, $amount, $description = null): \Stripe\PaymentIntent
    {
        try {
            $payment_intent = $this->stripe_client->paymentIntents->create([
                'amount' => $amount,
                'currency' => 'usd',
                'receipt_email' => $user->email,
                'description' => $description,
                'customer' => $user->user_card->customer,
                'payment_method' => $user->user_card->card,
                'payment_method_types' => ['card'],
            ]);
        } catch (\Exception $exception) {
            throw new ApiValidationException('Could not process payment. Please contact your bank and try again.');
        }
        return $payment_intent;
    }

    /**
     * @param null $email
     * @return \Stripe\Account
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createExpressAccount($email = null): \Stripe\Account
    {
        $this->initStripe();
        return \Stripe\Account::create([
            'country' => 'US',
            'type' => 'express',
            'email' => $email
        ]);
    }

    /**
     * @param $account_id
     * @return \Stripe\AccountLink
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createAccountLink($account_id): \Stripe\AccountLink
    {
        $this->initStripe();
        return \Stripe\AccountLink::create([
            'account' => $account_id,
            'refresh_url' => 'https://app.strobeart.com',
            'return_url' => 'https://app.strobeart.com/app/profile/settings',
            'type' => 'account_onboarding',
        ]);
    }

    /**
     * @param $account
     * @return \Stripe\LoginLink
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createLoginLinkAccount($account): \Stripe\LoginLink
    {
        $this->initStripe();
        return \Stripe\Account::createLoginLink($account['id']);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function connectedAccount($id)
    {
        $stripe = $this->getStripeInstance();
        return $stripe->account()->find($id);
    }


    /**
     * @param $amount
     * @param $account_id
     * @param $card_id
     * @param string $currency
     * @return \Stripe\Payout
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function payouts($amount, $account_id, $card_id, string $currency = 'usd'): \Stripe\Payout
    {
        $this->initStripe();
        return $this->stripe_client->payouts->create([
            'amount' => $amount,
            'currency' => $currency,
            'destination' => $card_id,
        ],
            ["stripe_account" => $account_id]);
    }

    /**
     * @param $amount
     * @param $account_id
     * @param string $transfer_group
     * @param string $currency
     * @return \Stripe\Transfer
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function transfer_money($amount, $account_id, string $transfer_group = '', string $currency = 'usd'): \Stripe\Transfer
    {
        $this->initStripe();
        return $this->stripe_client->transfers->create([
            'amount' => $amount,
            'currency' => $currency,
            'destination' => $account_id,
            'transfer_group' => $transfer_group,
        ]);
    }

    /**
     * @param $account
     * @return \Illuminate\Support\Collection|null
     */
    public function getCardsStripeAccount($account): ?\Illuminate\Support\Collection
    {
        $short_info = null;
        try {
            if ($account) {
                $account_stripe = $this->connectedAccount($account->connect_id);
                $external_accounts = $account_stripe['external_accounts']['data'];
                $short_info = collect($external_accounts)->map(function ($item) {
                    return [
                        'id' => $item['id'],
                        'brand' => $item['brand'] ?? '',
                        'last4' => $item['last4'],
                    ];
                });
            }
        } catch (\Exception $exception) {
            Log::info('ERROR getCardsStripeAccount: ' . $exception);
            return null;
        }

        return $short_info;
    }

    /**
     * @return Stripe
     */
    public function getStripeInstance(): Stripe
    {
        return new Stripe(config('services.stripe.secret_key'));
    }

    /**
     * @param $amount
     * @param $account_id
     * @param $currency
     * @return \Stripe\Transfer
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function transfer($amount, $account_id, $currency = 'usd'): \Stripe\Transfer
    {
        $this->initStripe();
        return \Stripe\Transfer::create([
            'amount' => $amount,
            'currency' => $currency,
            'destination' => $account_id,
        ]);
    }

    /**
     * @param $amount
     * @param $account_id
     * @return void
     */
    public function transferMoneyAccount($amount, $account_id): void
    {
        try {
            $this->transfer($amount, $account_id);
        } catch (\Exception $exception) {
            Log::info('ERROR transferMoneyAccount:' . $exception);
        }
    }

    /**
     * @return \Stripe\Balance
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function balance(): \Stripe\Balance
    {
        $this->initStripe();
        return $this->stripe_client->balance->retrieve();
    }

    /**
     *
     */
    public function initStripe(): void
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));
    }
}
