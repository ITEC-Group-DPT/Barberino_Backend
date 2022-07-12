<?php
include  __DIR__ . '/../includes/header.php';

if (isset($_POST['getinfo']))
{
    $query = '';
    if ($_POST['type'] == 'email')
    {
        $query = "client_email = ?";
    }
    else
    {
        $query = "phone_number = ?";
    }
    $data = trim($_POST['data']);


    $stmt = $conn->prepare("SELECT * FROM `clients` where " . $query);
    $stmt->bind_param("s", $data);
    $stmt->execute();
    $result = $stmt->get_result();
    $statistic = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($statistic);
}
else if (isset($_GET['artist']))
{
    $sql = "SELECT  employees.employee_id,  employees.first_name,  employees.last_name, COUNT(employees_schedule.day_id) FROM  employees,employees_schedule WHERE employees.employee_id = employees_schedule.employee_id GROUP by employees.employee_id,  employees.first_name,  employees.last_name; ";
    $result = $conn->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
}
else if (isset($_GET['selected_employee']) && isset($_GET['selected_services']))
{
    $desired_services = json_decode($_GET['selected_services']);

    //SELECTED EMPLOYEE
    $selected_employee = $_GET['selected_employee'];

    //Services Duration - End time expected
    $sum_duration = 0;
    $sql = "select sum(service_duration) from services where ";
    foreach ($desired_services as $service)
    {
        $sql .= "service_id = {$service} or ";
    }
    $sql = substr($sql, 0, -4);

    $result = $conn->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $sum_duration = intval($rows[0]["sum(service_duration)"]);

    $sum_duration = date('H:i', mktime(0, $sum_duration));
    $secs = strtotime($sum_duration) - strtotime("00:00:00");
    $open_time = date('H:i', mktime(6, 0, 0));

    $close_time = date('H:i', mktime(22, 0, 0));

    $start = $open_time;


    $result = date("H:i:s", strtotime($start) + $secs);

    $appointment_date = date('Y-m-d');

    $availableDate = [];
    while (count($availableDate) < 10)
    {
        $start = $open_time;
        $secs = strtotime($sum_duration) - strtotime("00:00:00");
        $result = date("H:i:s", strtotime($start) + $secs);
        $day_id = date('N', strtotime($appointment_date));
        $stmt = $conn->prepare("
                Select employee_id
                from employees_schedule
                where employee_id = ?
                and day_id = ?
        ");
        $stmt->bind_param("ss", $selected_employee, $day_id);
        $stmt->execute();
        $queryres = $stmt->get_result();
        // $emp = $queryres->fetch_all(MYSQLI_ASSOC); 
        if ($queryres->num_rows != 0) //avaiable day
        {
            array_push($availableDate, date("d-m-Y", strtotime($appointment_date)));
        }
        $appointment_date = date('Y-m-d', strtotime($appointment_date . ' +1 day'));
    }

    $timearr = getTimeForDate(isset($_GET['selectedDate']) ? $_GET['selectedDate'] : $availableDate[0], $sum_duration, $selected_employee, isset($_GET['selectedDate']) ? date("d-m-Y") == $_GET['selectedDate'] : date("d-m-Y") == $availableDate[0]);

    !isset($_GET['selectedDate']) && $res['date'] = $availableDate;
    $res['time'] = $timearr;
    echo json_encode($res);
    //get time for a day
}


function getTimeForDate($date, $sum_duration, $selected_employee, $isCurrentdate = false)
{
    global $conn;
    define("nexttimestep", 30); //next latest time slot from now
    define("timestep", 15); //booking time
    $timestep = constant("timestep");
    $appointment_date = date('Y-m-d', strtotime($date));
    $day_id = date('N', strtotime($appointment_date));

    $stmt = $conn->prepare("
        Select from_hour,to_hour
        from employees_schedule
        where employee_id = ?
        and day_id = ?
    ");
    $stmt->bind_param("ss", $selected_employee, $day_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $time = $res->fetch_all(MYSQLI_ASSOC);

    $open_time = date("H:i", strtotime($time[0]["from_hour"])); //can change open time in here
    $close_time = date("H:i", strtotime($time[0]["to_hour"]));

    if ($isCurrentdate)
        // $start = date("H:00");  //get time now by time zone
        if (isset($_GET['admin']))
        {
            $start = $open_time;
        }
        else
        {
            $step = constant("nexttimestep") - (int)date("i") % constant("nexttimestep"); //step in minute
            $start = date("H:i", strtotime(date("H:i")) + $step * 60);
        }
    else
        $start = $open_time;

    $secs = strtotime($sum_duration) - strtotime("00:00:00");
    $result = date("H:i", strtotime($start) + $secs);


    $timearr = [];
    while ($start < $close_time &&  $result <= $close_time)
    {
        //Check If there are no intersecting appointments with the current one
        if ($start >= $open_time)
        {
            $startDatetime = date("Y-m-d H:i:00", strtotime($appointment_date . " " . $start));
            $endDatetime = date("Y-m-d H:i:00", strtotime($appointment_date ." ". $result));
            $stmt = $conn->prepare("
                SELECT employees.employee_id id,employees.first_name firstname,employees.last_name lastname FROM employees
                except
                SELECT DISTINCT employees.employee_id,employees.first_name,employees.last_name
                from
                employees,
                appointments
                where
                employees.employee_id = appointments.employee_id
                and appointments.canceled = 0
                and date(appointments.start_time) = ?
                and (
                    (
                    ? >= appointments.start_time 
                    or ? > appointments.start_time 
                    )
                    and (
                    appointments.end_time_expected > ?
                    or appointments.end_time_expected >= ?
                    )
                )
            ");

            $stmt->bind_param("sssss", $appointment_date, $startDatetime, $endDatetime, $startDatetime, $endDatetime);
            $stmt->execute();

            $queryres = $stmt->get_result();

            if ($queryres->num_rows != 0)
            {
                $timearr[date("H:i", strtotime($start))] = mysqli_fetch_all($queryres,MYSQLI_ASSOC);
                
            }
            else
            {
                
            }
        }
        $start = strtotime("+{$timestep} minutes", strtotime($start));
        $start =  date('H:i', $start);

        $secs = strtotime($sum_duration) - strtotime("00:00:00");
        $result = date("H:i", strtotime($start) + $secs);
    }
    return $timearr;
}
