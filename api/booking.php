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
else if (isset($_GET['services']))
{
    $sql = "SELECT service_id,service_name , service_duration FROM services ";
    $result = $conn->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($rows);
}
else if (isset($_GET['selected_services']))
{
    $returnarr=[];
    if ($_GET['option'] == "getDateTime")
    {
        $appointment_date = date('Y-m-d');
        $availableDate = [];
        while (count($availableDate) < 10)
        {
            $day_id = date('N', strtotime($appointment_date));
            $stmt = $conn->prepare("
                                    SELECT count(*)
                                    FROM employees_schedule
                                    where day_id = ?
                                    having COUNT(*) > 0;
              ");
            $stmt->bind_param("s", $day_id);
            $stmt->execute();
            $queryres = $stmt->get_result();
            if (mysqli_num_rows($queryres) != 0) //avaiable day
            {
                array_push($availableDate, date("d-m-Y", strtotime($appointment_date)));
            }
            $appointment_date = date('Y-m-d', strtotime($appointment_date . ' +1 day'));
        }
        $returnarr['AvailableDates'] = $availableDate;
    }

    //$_GET['option'] == "getTimeByDate"
    $DateForTimeSlot = $_GET['option'] == "getTimeByDate" ? $_GET['appointmentDate'] : $availableDate[0];
    $timearr = getTimeForDate($DateForTimeSlot);

    $returnarr["timeslot_$DateForTimeSlot"] = $timearr;
    echo json_encode($returnarr);
    //get time for a day
}


function getTimeForDate($appointment_date)
{
    global $conn;

    //counttimeduration

    $desired_services = json_decode($_GET['selected_services']);

    $sum_duration = 0;
    $sql = "select sum(service_duration) from services where ";
    foreach ($desired_services as $service)
    {
        $sql .= "service_id = {$service} or ";
    }
    $sql = substr($sql, 0, -4);

    $result = $conn->query($sql);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $sum_duration = intval($rows[0]["sum(service_duration)"]);
    $sum_duration = date('H:i', mktime(0, $sum_duration));

    //counttimeduration


    define("nexttimestep", 30); //next latest time slot from now
    define("timestep", 15); //booking time
    $timestep = constant("timestep");


    $appointment_date = date('Y-m-d', strtotime($appointment_date));

    $open_time = date("H:i", strtotime('09:00')); //can change open time in here
    $close_time = date("H:i", strtotime('18:00'));

    // $start = date("H:00");  //get time now by time zone
    // admin can book for guest (at time not present in list)
    if (date("d-m-Y") <> $appointment_date or isset($_GET['admin']))
    {
        $start = $open_time;
    }
    else
    {
        $step = constant("nexttimestep") - (int)date("i") % constant("nexttimestep"); //next earliest timeslot from current (3)
        $start = date("H:i", strtotime(date("H:i")) + $step * 60);
    }


    $secs = strtotime($sum_duration) - strtotime("00:00:00");
    $estimatedEndtime = date("H:i", strtotime($start) + $secs);


    $timearr = [];
    while ($start < $close_time &&  $estimatedEndtime <= $close_time)
    {
        //Check If there are no intersecting appointments with the current one
        if ($start >= $open_time)
        {
            $startDatetime = date("Y-m-d H:i:00", strtotime($appointment_date . " " . $start));
            $endDatetime = date("Y-m-d H:i:00", strtotime($appointment_date . " " . $estimatedEndtime));
            //weekday return 0 = Monday, 1 = Tuesday, 2 = Wednesday, 3 = Thursday, 4 = Friday, 5 = Saturday, 6 = Sunday. but the database store monday = 1 so need +1


            $stmt = $conn->prepare("
            SELECT
            employees.employee_id id,
            employees.first_name firstname,
            employees.last_name lastname
            FROM employees, employees_schedule
            where employees.employee_id = employees_schedule.employee_id
            and employees_schedule.day_id = weekday(date(?))+1 and 
            employees_schedule.from_hour <= time(?) and time(?) <= employees_schedule.to_hour
            except
            SELECT
                employees.employee_id id,
                employees.first_name firstname,
                employees.last_name lastname
            from employees, appointments
            where
                employees.employee_id = appointments.employee_id
                and appointments.canceled = 0
                and date(appointments.start_time) = date(?)
                and ( (
                        ? >= appointments.start_time
                        or ? > appointments.start_time
                    )
                    and (
                        appointments.end_time_expected > ?
                        or appointments.end_time_expected >= ?
                    )
                )
            ");

            $stmt->bind_param("ssssssss",$startDatetime,$startDatetime, $endDatetime, $appointment_date, $startDatetime, $endDatetime, $startDatetime, $endDatetime);
            $stmt->execute();

            $queryres = $stmt->get_result();

            if ($queryres->num_rows != 0)
            {
                $timearr[date("H:i", strtotime($start))] = mysqli_fetch_all($queryres, MYSQLI_ASSOC);
            }
            else
            {
                // $timearr[date("H:i", strtotime($start))] = [];
            }
        }
        $start = strtotime("+{$timestep} minutes", strtotime($start));
        $start =  date('H:i', $start);

        $secs = strtotime($sum_duration) - strtotime("00:00:00");
        $result = date("H:i", strtotime($start) + $secs);
    }
    return $timearr;
}
