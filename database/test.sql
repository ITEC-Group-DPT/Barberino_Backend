-- Active: 1657382232650@@217.21.74.1@3306@u448613383_theshear2

-- -- danh sach thang ban

-- -- SELECT employees.employee_id,employees.first_name,employees.last_name FROM employees

-- -- except

-- -- select

-- --   employees.employee_id,employees.first_name,employees.last_name

-- SELECT count(*)

-- FROM employees_schedule

-- where day_id = 8

-- having COUNT(*) > 0;

SET time_zone = '+07:00';

-- SET @bd = '2022-07-21 13:00:00';

-- set @kt = '2022-07-21 13:15:00';

-- SELECT

--     employees.employee_id id,

--     employees.first_name firstname,

--     employees.last_name lastname

-- FROM employees, employees_schedule

-- where employees.employee_id = employees_schedule.employee_id

-- and employees_schedule.day_id = weekday(date(@bd))+1 and

-- employees_schedule.from_hour <= time(@bd) and time(@kt) <= employees_schedule.to_hour

-- except

-- SELECT

--     employees.employee_id id,

--     employees.first_name firstname,

--     employees.last_name lastname

-- from employees, appointments

-- where

--     employees.employee_id = appointments.employee_id

--     and appointments.canceled = 0

--     and date(appointments.start_time) = date(@bd)

--     and ( (

--             @bd >= appointments.start_time

--             or @kt > appointments.start_time

--         )

--         and (

--             appointments.end_time_expected > @bd

--             or appointments.end_time_expected >= @kt

--         )

--     )

SELECT
    appointments.appointment_id id,
    CONCAT(
        clients.first_name,
        ' ',
        clients.last_name
    ) cusName,
    clients.phone_number phoneNum,
    appointments.date_created dateCreated,
    appointments.start_time startDate,
    appointments.end_time_expected endDate,
    concat(
        employees.first_name,
        ' ',
        employees.last_name
    ) empName
FROM
    appointments,
    clients,
    employees
WHERE
    appointments.canceled = 0
    and appointments.start_time >= DATE_SUB(now(), INTERVAL 1 hour)
    and clients.client_id = appointments.client_id
    and employees.employee_id = appointments.employee_id
ORDER BY
    appointments.start_time;

-- SELECT services.service_name

-- FROM

--     services_booked,

--     services

-- where

--     services_booked.service_id = services.service_id

--     and services_booked.appointment_id = 85

SELECT
    SQL_CALC_FOUND_ROWS *, appointments.appointment_id as id,
    CONCAT(
        clients.first_name,
        ' ',
        clients.last_name
    ) cusName,
    clients.phone_number phoneNum,
    appointments.date_created dateCreated,
    appointments.start_time startDate,
    appointments.end_time_expected endDate,
    concat(
        employees.first_name,
        ' ',
        employees.last_name
    ) empName,
    Case
        when appointments.canceled = 1 then 'cancelled'
        when appointments.start_time >= DATE_SUB(now(), INTERVAL 30 minute) then 'ongoing'
        else 'overdue'
    end as status
FROM
    appointments,
    clients,
    employees
WHERE
    clients.client_id = appointments.client_id
    and employees.employee_id = appointments.employee_id
ORDER BY
    appointments.start_time desc
LIMIT 4;
SELECT FOUND_ROWS();

SELECT
    count(
        if(
            appointments.canceled = 1,
            1,
            NUll
        )
    ) 'Cancelled',
    count(
        if(
            appointments.canceled = 0
            and appointments.start_time >= DATE_SUB(now(), INTERVAL 15 MINUTE),
            1,
            NUll
        )
    ) 'Ongoing',
    count(
        if(
            appointments.canceled = 0
            and appointments.start_time < DATE_SUB(now(), INTERVAL 15 MINUTE),
            1,
            NUll
        )
    ) 'Overdue',
    count(*) 'Total'
FROM appointments;

UPDATE appointments SET canceled = 1 WHERE appointment_id = 12;