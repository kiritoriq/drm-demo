<?php

return [
    'contractor' => [
        'task' => [
            'created' => [
                'title' => 'You got new Job',
                'content' => 'Task with number :task_number is successfully created and assigned to You.'
            ],
            'accepted' => [
                'title' => 'Job Accepted',
                'content' => 'You have accepted Task with number :task_number. Please make Site Visit to continue this Task.'
            ],
            'reported' => [
                'title' => 'Issue Reported',
                'content' => 'You have submit the issue and quotation for Task with number :task_number.'
            ],
            'awarded' => [
                'title' => 'Job Awarded',
                'content' => 'You awarded to the Task with number :task_number. Please start the progress to set Task status as started.'
            ],
            'started' => [
                'title' => 'Job Started',
                'content' => 'You started :task_number Task at :date_time'
            ],
            'progress_completed' => [
                'title' => 'Job Completed',
                'content' => 'Your progress of this :task_number Task is completed. DRM will do QC Progress to check Your work.'
            ],
            'qc_progress' => [
                'title' => 'QC Progress Started',
                'content' => 'DRM started QC Progress to Your job with number :task_number.'
            ],
            'finished' => [
                'title' => 'Job Finished',
                'content' => 'Congratulations, Your job with number :task_number is finished. All of the cost You submit before will store at Your wallet.'
            ],
            'rejected' => [
                'title' => 'Job Rejected',
                'content' => 'Task with number :task_number that assigned to You has been Rejected.'
            ],
            'failed' => [
                'title' => 'Job Failed',
                'content' => 'Task with number :task_number that assigned to You has been Failed. You can\'t do any further action to this Task.'
            ]
        ],
        'wallet' => [
            'credited' => [
                'title' => 'Wallet Credited',
                'content' => 'Admin has credited :amount of :task_number to you'
            ]
        ]
    ],
    'web' => [
        'ticket' => [
            'created' => [
                'title' => 'New Ticket is Created',
                'content' => ':ticket_number is created at :date_time'
            ],
            'assigned' => [
                'title' => 'Ticket is Assigned',
                'content' => ':ticket_number is assigned to :user_name & :project at :date_time'
            ],
            'unassigned' => [
                'title' => 'Ticket Unassigned',
                'content' => ':ticket_number is no longer assigned to :user_name'
            ],
            'status_updated' => [
                'title' => 'Ticket Status is Updated',
                'content' => ':ticket_number status is updated to :status at :date_time'
            ]
        ],
        'site_visit' => [
            'created' => [
                'title' => 'Site Visit is Created',
                'content' => ':ticket_number \'s site visit created & assigned to :contractor at :date_time'
            ],

        ],
        'quotation' => [
            'created' => [
                'title' => 'Quotation for :ticket_number is Created',
                'content' => 'Quotation raised by :user_name at :date_time'
            ],
            'accepted' => [
                'title' => 'Quotation Accepted',
                'content' => 'Quotation of :ticket_number is accepted by Customer'
            ],
            'rejected' => [
                'title' => 'Quotation Rejected',
                'content' => 'Quotation of :ticket_number is rejected by Customer'
            ]
        ],
        'task' => [
            'created' => [
                'title' => 'Task for :ticket_number is Created',
                'content' => ':task_number is created at :date_time'
            ],
            'status_updated' => [
                'title' => 'Task Status is Updated',
                'content' => ':task_number status is updated to :status at :date_time'
            ],
            'assignee_updated' => [
                'title' => 'Task Assignee is Updated',
                'content' => ':task_number assignee has been updated to :contractor at :date_time'
            ],
            'site_visit_feedback' => [
                'title' => 'Task :task_number Status is Updated',
                'content' => ':contractor uploaded images & send you a quote of :ticket_number at :date_time'
            ]
        ],
        'settings' => [
            'ticket_due' => [
                'title' => 'Ticket Due Settings Updated',
                'content' => ':priority changed to :days days'
            ],
            'branches' => [
                'created' => [
                    'title' => 'New Branches Created',
                    'content' => ':branch_name has been created'
                ],
            ],
            'contractor' => [
                'created' => [
                    'title' => 'New Contractor Created',
                    'content' => ':contractor_name has been created'
                ]
            ],
            'customer' => [
                'created' => [
                    'title' => 'New Customer Created',
                    'content' => ':customer_name has been created'
                ]
            ],
            'user' => [
                'created' => [
                    'title' => 'New User Created',
                    'content' => ':user_name has been created'
                ]
            ]
        ]
    ]
];