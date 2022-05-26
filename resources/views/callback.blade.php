<html>
<head>
    <meta charset="utf-8">
    <script>
        window.token = (function () {
            return @json($token)
        })();
        window.user = (function () {
            return @json($user)
        })();
        window.registration= (function () {
            return @json($registration)
        })();

        if (window.user && window.token) {
            localStorage.setItem('strobeart_user', window.user);
            localStorage.setItem('strobeart_jwt', window.token);
            if (window.registration){
                localStorage.setItem('strobeart_registration', true);
                window.location.href = `${window.location.origin}/app/registration`
            }else{
                window.location.href = `${window.location.origin}/app`
            }
        }

    </script>
</head>
<body>
</body>
</html>
