<?php
include  __DIR__ . '/../includes/header.php';

if (isset($_POST['option']) and $_POST['option'] == 'createAppointment')
{
    $arrName = explode(' ', trim($_POST['name']), 2);
    $firstName = $arrName[0];
    $lastName = count($arrName) == 1 ? "" : $arrName[1];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $userid = -1;

    $stmt = $conn->prepare("SELECT client_id as id FROM `clients` where client_email = ?");
    $stmt->bind_param("s", $_POST['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    $statistic = mysqli_fetch_all($result,MYSQLI_ASSOC);
    if (count($statistic) > 0)
    {
        $userid = $statistic[0]['id'];
       
        $query = $conn->prepare("UPDATE `clients` SET `first_name`= ?, `last_name`=?, `phone_number`= ? WHERE `client_email`= ?");
        $query->bind_param("ssss", $firstName, $lastName, $phone, $email);
        $query->execute();
    }
    else
    {
        $query = $conn->prepare("INSERT INTO `clients`(`first_name`, `last_name`, `phone_number`, `client_email`) VALUES (?,?,?,?)");
        $query->bind_param("ssss", $firstName, $lastName, $phone, $email);
        $query->execute();
        $userid = $query->insert_id;
    }
    $selected_services = json_decode($_POST['selected_services']);

    $sum_duration = 0;
    $sql = "select sum(service_duration) from services where ";
    foreach ($selected_services as $service)
    {
        $sql .= "service_id = {$service} or ";
    }
    $sql = substr($sql, 0, -4);

    $result = $conn->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $sum_duration = intval($rows[0]["sum(service_duration)"]);
    $sum_duration = date('H:i', mktime(0, $sum_duration));
    $secs = strtotime($sum_duration) - strtotime("00:00:00");

    $start_time = date("Y-m-d H:i", strtotime($_POST['selected_date']." ".$_POST['selected_time']) );
    $end_time = date("Y-m-d H:i", strtotime($start_time) +  $secs);

    $stmt_appointment = $conn->prepare("insert into appointments(date_created, client_id, employee_id, start_time, end_time_expected ) values(?, ?, ?, ?, ?)");
    $stmt_appointment->execute(array(Date("Y-m-d H:i"),$userid,$_POST['selected_employee'],$start_time,$end_time));
    $appointment_id = $stmt_appointment->insert_id;

    foreach($selected_services as $service)
    {
        $stmt = $conn->prepare("insert into services_booked(appointment_id, service_id) values(?, ?)");
        $stmt->execute(array($appointment_id,$service));
    }   
    echo 'success';
}
 