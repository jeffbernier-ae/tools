<?php
/**
 * @var string $currentDT
 */
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
DB::query("TRUNCATE accessibility_champion_scores");
$rs = DB::query("SELECT participant_id, course_id, quiz_score, scheduled_date, scheduled_time
                    FROM participants p 
                        JOIN event_registrations er USING (participant_id) 
                        JOIN event_registrations_courses erc using (event_registration_id) 
                        JOIN event_courses USING (event_course_id) 
                        JOIN courses c USING (course_id) 
                    WHERE attended = 1"
);
foreach ($rs as $r) :
    $check = DB::queryFirstRow("SELECT quiz_score 
                            FROM accessibility_champion_scores 
                            WHERE participant_id = %i AND course_id = %i",
                            $r['participant_id'], $r['course_id']
    );
$classTime = new DateTime($r['scheduled_date'] . ' ' . $r['scheduled_time']);
    $data['participant_id'] = $r['participant_id'];
    $data['course_id'] = $r['course_id'];
    if (DB::count() == 0) :
        $data['quiz_score'] = $r['quiz_score'];
    elseif ($check['quiz_score'] > $r['quiz_score']) :
        $data['quiz_score'] = $check['quiz_score'];
    else :
        $data['quiz_score'] = $check['quiz_score'];
    endif;
    $data['dtmTaken'] = $classTime->format('Y-m-d H:i:s');
    DB::insertUpdate('accessibility_champion_scores', $data);
    unset ($data, $check);
endforeach;
echo 'complete';
