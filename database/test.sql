-- -- danh sach thang ban

-- -- SELECT employees.employee_id,employees.first_name,employees.last_name FROM employees

-- -- except

-- -- select

-- --   employees.employee_id,employees.first_name,employees.last_name
-- SELECT count(*)
-- FROM employees_schedule
-- where day_id = 8
-- having COUNT(*) > 0;
SET @bd = '2022-07-19 08:30:00';

set @kt = '2022-07-19 08:45:00';

SELECT
    employees.employee_id id,
    employees.first_name firstname,
    employees.last_name lastname
FROM employees, employees_schedule
where employees.employee_id = employees_schedule.employee_id
and employees_schedule.day_id = weekday(date(@bd))+1 and 
employees_schedule.from_hour <= time(@bd) and time(@kt) <= employees_schedule.to_hour
except
SELECT
    employees.employee_id id,
    employees.first_name firstname,
    employees.last_name lastname
from employees, appointments
where
    employees.employee_id = appointments.employee_id
    and appointments.canceled = 0
    and date(appointments.start_time) = date(@bd)
    and ( (
            @bd >= appointments.start_time
            or @kt > appointments.start_time
        )
        and (
            appointments.end_time_expected > @bd
            or appointments.end_time_expected >= @kt
        )
    )

-- SELECT service_id,service_name, service_duration FROM services