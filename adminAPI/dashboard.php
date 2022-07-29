<?php
include  __DIR__ . '/../includes/header.php';
if (isset($_GET["option"]))
{
    if ($_GET["option"] == "appointments" or $_GET['option'] == 'getPages')
    {
        $limit = 4;
        $delaytime = "15 MINUTE";

        if ($_GET['sortby'] == 'All Booking')
        {
            $query = " ORDER BY appointments.start_time desc ";
        }
        else if ($_GET['sortby'] == 'Ongoing')
        {
            $query = " and appointments.canceled = 0
            and appointments.start_time >= DATE_SUB(now(), INTERVAL {$delaytime})  ORDER BY appointments.start_time ";
        }
        else if ($_GET['sortby'] == 'Completed')
        {
            $query = " and appointments.canceled = -1
            ORDER BY appointments.start_time desc ";
        }
        else if ($_GET['sortby'] == 'Overdue')
        {
            $query = " and appointments.canceled = 0
            and appointments.start_time < DATE_SUB(now(), INTERVAL {$delaytime})  ORDER BY appointments.start_time desc";
        }
        else if ($_GET['sortby'] == 'Cancelled')
        {
            $query = " and appointments.canceled = 1
            ORDER BY appointments.start_time desc";
        }

        if ($_GET['option'] == 'getPages')
        {
            $result = $conn->query("SELECT ceil(count(*)*1.0/{$limit}) totalPages from appointments where 1 {$query}");
            $arr = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $res = $arr[0];
            echo json_encode($res);
            exit;
        }
        
        $offset = 4 * ($_GET['page'] - 1);
        $limitStr = " Limit {$offset}, {$limit}";
        $query = $query . $limitStr;


        $sql = "SELECT
        appointments.appointment_id id,
        CONCAT(
            clients.first_name,
            ' ',
            clients.last_name
        ) cusName,
        clients.phone_number phoneNum,
        appointments.start_time startDate,
        appointments.end_time_expected endDate,
        concat(
            employees.first_name,
            ' ',
            employees.last_name
        ) empName,
        Case 
            when appointments.canceled = 1 then 'Cancelled' 
            when appointments.canceled = -1 then 'Completed'
            when appointments.start_time >= DATE_SUB(now(), INTERVAL {$delaytime}) then 'Ongoing'
            else 'Overdue'
        end as status
        FROM
        appointments,
        clients,
        employees
        WHERE
        clients.client_id = appointments.client_id
        and employees.employee_id = appointments.employee_id " . $query;

        $result = $conn->query($sql);
        $arr = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $res = [];
        foreach ($arr as $app)
        {
            $appointment = [];
            foreach ($app as $key => $value)
            {
                $appointment[$key] = $value;
            }
            $sql = "SELECT services.service_name
            FROM
                services_booked,
                services
            where
                services_booked.service_id = services.service_id
                and services_booked.appointment_id = {$appointment['id']}";

            $result = $conn->query($sql);
            $services = mysqli_fetch_all($result, MYSQLI_NUM);
            $appointment['services'] = [];
            foreach ($services as $service)
            {
                array_push($appointment['services'], $service[0]);
            }
            array_push($res, $appointment);
        }
        echo json_encode($res);
    }
    elseif ($_GET["option"] == "statistic")
    {
        $sql = "SELECT
                    count(if(appointments.canceled = 1 ,1 ,NUll)) 'Cancelled',
                    count(if(appointments.canceled = 0 and appointments.start_time >= DATE_SUB(now(), INTERVAL 15 MINUTE),1 ,NUll)) 'Ongoing',
                    count(if(appointments.canceled = 0 and appointments.start_time < DATE_SUB(now(), INTERVAL 15 MINUTE),1 ,NUll)) 'Overdue',
                    count(if(appointments.canceled = -1 ,1 ,NUll)) 'Completed',
                    count(*) 'Total'
                FROM
                    appointments
                ";
        $result = $conn->query($sql);
        $stas = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $res = [];
        foreach ($stas[0] as $key => $value)
        {
            $arr = null;
            $arr['title'] = $key;
            $arr['value'] = $value;
            array_push($res, $arr);
        }
        echo json_encode($res);
    }
}
else if (isset($_POST["option"]))
{
    if ($_POST['option'] = 'changeStatus')
    {
        if ($_POST['status'] == 'Completed')
        {
            $status = -1;
        }
        elseif ($_POST['status'] == 'Cancelled')
        {
            $status = 1;
        }
        $sql = "UPDATE appointments SET appointments.canceled = {$status} WHERE appointments.canceled = 0 and appointment_id = {$_POST['id']}";
        $result = $conn->query($sql);
        echo "success";
    }
}
