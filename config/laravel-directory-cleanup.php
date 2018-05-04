<?php

return [

    'directories' => [

        /*
         * Here you can specify which directories need to be cleanup. All files older than
         * the specified amount of minutes will be deleted.
         */


        'public/temp' => [
            'deleteAllOlderThanMinutes' => (int)env('DELETE_OLDER_THAN')
        ],
    ],
];
