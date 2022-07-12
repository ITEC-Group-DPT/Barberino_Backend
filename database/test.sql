-- danh sach thang ban

-- SELECT employees.employee_id,employees.first_name,employees.last_name FROM employees

-- except

-- select

--   employees.employee_id,employees.first_name,employees.last_name

SET @bd = '2022-07-11 09:00:00';

set @kt = '2022-07-11 10:30:00';

SELECT
    employees.employee_id id,
    employees.first_name firstname,
    employees.last_name lastname
FROM employees
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