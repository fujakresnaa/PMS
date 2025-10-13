<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WBSController extends Controller
{
    public function get()
    {
        $json = [
            "project" => [
                "start_date" => "2021-05-10",
                "end_date" => "2021-06-31",
                "grid_length" => 51
            ],
            "grid" => [
                "may" => [
                    "start_date" => 10,
                    "length" => 20
                ],
                "juni" => [
                    "start_date" => 1,
                    "length" => 31
                ]
            ],
            "task" => [
                [
                    "task_code" => "", // WIP
                    "task_name" => 'Header Task 1',
                    "start_from" => "2021-05-10",
                    "end_from" => "2021-05-28",
                    "grid_start" => 0,
                    "grid_width" => 30,
                    "length" => 19,
                    "index" => 0,
                    "bgc" => "#71d5f9",
                    "child" => [
                        [
                            "task_name" => 'Level 1 Task 1',
                            "start_from" => "2021-05-10",
                            "end_from" => "2021-05-15",
                            "grid_start" => 0,
                            "grid_width" => 30,
                            "length" => 6,
                            "index" => 1,
                            "bgc" => "#ff73c7",
                            "child" => [
                                [
                                    "task_name" => 'Level 2 Task 1',
                                    "start_from" => "2021-05-10",
                                    "end_from" => "2021-05-12",
                                    "grid_start" => 0,
                                    "grid_width" => 30,
                                    "length" => 3,
                                    "index" => 2,
                                    "bgc" => "#f7b84f",
                                    "child" => []
                                ],
                                [
                                    "task_name" => 'Level 2 Task 2',
                                    "start_from" => "2021-05-12",
                                    "end_from" => "2021-05-15",
                                    "grid_start" => 2,
                                    "grid_width" => 30,
                                    "length" => 4,
                                    "index" => 2,
                                    "bgc" => "#f7b84f",
                                    "child" => []
                                ]
                            ]
                        ],
                        [
                            "task_name" => 'Level 1 Task 2',
                            "start_from" => "2021-05-16",
                            "end_from" => "2021-05-23",
                            "grid_start" => 7,
                            "grid_width" => 30,
                            "length" => 8,
                            "index" => 1,
                            "bgc" => "#ff73c7",
                            "child" => []
                        ]
                    ]
                ]
            ]
        ];
        return H_apiResponse($json);
    }

    public function get3()
    {
        $json = [
            "project" => [
                "name" => "PMP",
                "start_date" => "2021-05-10",
                "end_date" => "2021-06-31",
                "grid_length" => 51
            ],
            "grid" => [
                "may" => [
                    "start_date" => 10,
                    "length" => 20
                ],
                "juni" => [
                    "start_date" => 1,
                    "length" => 31
                ]
            ],
            "task" => [
                [
                    "task_code" => "", // WIP
                    "task_name" => 'Header Task 1',
                    "start_from" => "2021-05-10",
                    "end_from" => "2021-05-28",
                    "grid_start" => 0,
                    "grid_width" => 30,
                    "length" => 19,
                    "actual" => 0,
                    "index" => 0,
                    "bgc" => "#71d5f9",
                    "collapse" => true,
                    "child" => [
                        [
                            "task_name" => 'Level 1 Task 1',
                            "start_from" => "2021-05-10",
                            "end_from" => "2021-05-15",
                            "grid_start" => 0,
                            "grid_width" => 30,
                            "length" => 6,
                            "actual" => 0,
                            "index" => 1,
                            "bgc" => "#ff73c7",
                            "collapse" => true,
                            "child" => [
                                [
                                    "task_name" => 'Level 2 Task 1',
                                    "start_from" => "2021-05-10",
                                    "end_from" => "2021-05-12",
                                    "grid_start" => 0,
                                    "grid_width" => 30,
                                    "length" => 3,
                                    "actual" => 3,
                                    "index" => 2,
                                    "bgc" => "#f7b84f",
                                    "collapse" => false,
                                    "child" => []
                                ],
                                [
                                    "task_name" => 'Level 2 Task 2',
                                    "start_from" => "2021-05-12",
                                    "end_from" => "2021-05-15",
                                    "grid_start" => 2,
                                    "grid_width" => 30,
                                    "length" => 4,
                                    "actual" => 6,
                                    "index" => 2,
                                    "bgc" => "#f7b84f",
                                    "child" => [],
                                    "collapse" => false,
                                    "activities" => [
                                        [
                                            "date" => "2021-05-12",
                                            "start" => 1,
                                            "detail" => [
                                                [
                                                    "name" => "Setup Modul",
                                                    "description" => "Setup Customer Module",
                                                    "date" => "2021-06-25 22:30:00"
                                                ]
                                            ]
                                        ],
                                        [
                                            "date" => "2021-05-13",
                                            "start" => 2,
                                            "detail" => [
                                                [
                                                    "name" => "Deliver Modul",
                                                    "description" => "Deliver Customer Module",
                                                    "date" => "2021-05-13 22:30:00"
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            "task_name" => 'Level 1 Task 2',
                            "start_from" => "2021-05-16",
                            "end_from" => "2021-05-23",
                            "grid_start" => 7,
                            "grid_width" => 30,
                            "length" => 8,
                            "actual" => 0,
                            "index" => 1,
                            "bgc" => "#ff73c7",
                            "collapse" => false,
                            "child" => []
                        ]
                    ]
                ]
            ]
        ];
        return H_apiResponse($json);
    }

    public function get2()
    {
        $json = [
            "project" => [
                "name" => "TUG BOAT 2200 HP",
                "start_date" => "2021-05-28",
                "end_date" => "2021-06-03",
                "grid_length" => 6
            ],
            "grid" => [
                "may" => [
                    "start_date" => 28,
                    "length" => 3
                ],
                "juni" => [
                    "start_date" => 1,
                    "length" => 3
                ]
            ],

            "task" => [
                [
                    "code" => "A",
                    "name" => "General Service",
                    "collapse" => false,
                    "detail" => [
                        [
                            "name" => "kapal sandar",
                            "bobot" => 20,
                            "progress" => 30,
                            "start_date" => "2021-05-28",
                            "end_date" => "2021-05-29",
                            "day" => 2,
                            "grid_start" => 0,
                            "remark" => "blabla..",
                            "plan" => [
                                [
                                    "progress" => 50,
                                    "date" => "2021-05-28",
                                    "grid_start" => 0,
                                    "detail" => [
                                        [
                                            "name" => "Plating Deck",
                                            "progress" => 50,
                                            "date" => "2021-05-28 09:00",
                                        ]
                                    ]
                                ],
                                [
                                    "progress" => 50,
                                    "date" => "2021-05-29",
                                    "grid_start" => 1,
                                    "actual" => [
                                        [
                                            "name" => "Finishing",
                                            "progress" => 50,
                                            "date" => "2021-05-29 09:00",
                                        ]
                                    ]
                                ]
                            ],
                            "actual" => [
                                [
                                    "progress" => 50,
                                    "date" => "2021-05-28",
                                    "grid_start" => 0,
                                    "detail" => [
                                        [
                                            "name" => "Plating Deck",
                                            "progress" => 30,
                                            "date" => "2021-05-28 09:00",
                                        ],
                                        [
                                            "name" => "Completing",
                                            "progress" => 20,
                                            "date" => "2021-05-28 14:00",
                                        ]
                                    ]
                                ],
                                [
                                    "progress" => 50,
                                    "date" => "2021-05-29",
                                    "grid_start" => 1,
                                    "actual" => [
                                        [
                                            "name" => "Finishing",
                                            "progress" => 30,
                                            "date" => "2021-05-29 09:00",
                                        ],
                                        [
                                            "name" => "Finishing All",
                                            "progress" => 20,
                                            "date" => "2021-05-29 14:00",
                                        ]
                                    ]
                                ]
                            ],
                        ],
                        [
                            "name" => "Mooring / Unmooring ambyraisdi",
                            "bobot" => 20,
                            "progress" => 30,
                            "start_date" => "2021-05-30",
                            "end_date" => "2021-05-30",
                            "day" => 1,
                            "grid_start" => 2,
                            "remark" => "blabla..",
                            "plan" => [
                                [
                                    "progress" => 50,
                                    "date" => "2021-05-30",
                                    "grid_start" => 2,
                                    "detail" => [
                                        [
                                            "name" => "Plating Deck",
                                            "progress" => 50,
                                            "date" => "2021-05-28 09:00",
                                        ]
                                    ]
                                ],
                            ],
                            "actual" => [
                                [
                                    "progress" => 50,
                                    "date" => "2021-05-28",
                                    "grid_start" => 2,
                                    "detail" => [
                                        [
                                            "name" => "Plating Deck",
                                            "progress" => 30,
                                            "date" => "2021-05-28 09:00",
                                        ],
                                        [
                                            "name" => "Completing",
                                            "progress" => 20,
                                            "date" => "2021-05-28 14:00",
                                        ]
                                    ]
                                ],
                            ]
                        ]
                    ]
                ],
                [
                    "code" => "B",
                    "name" => "Docking Service",
                    "collapse" => false,
                    "detail" => [
                        [
                            "name" => "Swagger Opening",
                            "bobot" => 20,
                            "progress" => 30,
                            "start_date" => "2021-05-30",
                            "end_date" => "2021-06-03",
                            "day" => 4,
                            "grid_start" => 1,
                            "remark" => "blabla..",
                            "plan" => [
                                [
                                    "progress" => 50,
                                    "date" => "2021-05-30",
                                    "grid_start" => 2,
                                    "detail" => [
                                        [
                                            "name" => "Plating Deck",
                                            "progress" => 50,
                                            "date" => "2021-05-28 09:00",
                                        ]
                                    ]
                                ],
                                [
                                    "progress" => 50,
                                    "date" => "2021-06-01",
                                    "grid_start" => 3,
                                    "actual" => [
                                        [
                                            "name" => "Finishing",
                                            "progress" => 50,
                                            "date" => "2021-05-29 09:00",
                                        ]
                                    ]
                                ],
                                [
                                    "progress" => 50,
                                    "date" => "2021-06-02",
                                    "grid_start" => 4,
                                    "actual" => [
                                        [
                                            "name" => "Finishing All",
                                            "progress" => 50,
                                            "date" => "2021-05-29 09:00",
                                        ]
                                    ]
                                ],
                                [
                                    "progress" => 50,
                                    "date" => "2021-06-03",
                                    "grid_start" => 5,
                                    "actual" => [
                                        [
                                            "name" => "Cleaning",
                                            "progress" => 50,
                                            "date" => "2021-05-29 09:00",
                                        ]
                                    ]
                                ]
                            ],
                            "actual" => [
                                [
                                    "progress" => 50,
                                    "date" => "2021-05-30",
                                    "grid_start" => 2,
                                    "detail" => [
                                        [
                                            "name" => "Plating Deck",
                                            "progress" => 50,
                                            "date" => "2021-05-28 09:00",
                                        ]
                                    ]
                                ],
                                [
                                    "progress" => 50,
                                    "date" => "2021-06-01",
                                    "grid_start" => 3,
                                    "actual" => [
                                        [
                                            "name" => "Finishing",
                                            "progress" => 50,
                                            "date" => "2021-05-29 09:00",
                                        ]
                                    ]
                                ],
                                [
                                    "progress" => 50,
                                    "date" => "2021-06-02",
                                    "grid_start" => 4,
                                    "actual" => [
                                        [
                                            "name" => "Finishing All",
                                            "progress" => 50,
                                            "date" => "2021-05-29 09:00",
                                        ]
                                    ]
                                ],
                                [
                                    "progress" => 50,
                                    "date" => "2021-06-03",
                                    "grid_start" => 5,
                                    "actual" => [
                                        [
                                            "name" => "Cleaning",
                                            "progress" => 50,
                                            "date" => "2021-05-29 09:00",
                                        ]
                                    ]
                                ]
                            ],
                        ]
                    ]
                ]
            ]
        ];
        return H_apiResponse($json);
    }
}
