export default class TabsEnum {
    static ADDNEWJOB = [
        {
            title: 'Add Files',
            description: 'Please upload all files that you need to be edited.',
        },
        {
            title: 'Add Requirements',
            description: 'Please add any details that would help the editor better your job.',
        },
        {
            title: 'Add Due Date',
            description: 'Please select the due date and time for your job.',
        }
    ];

    static INDEXES = {
        add_file: 0,
        add_requirements: 1,
        add_due_date: 2,
    };
}
