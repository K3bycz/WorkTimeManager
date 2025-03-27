# Work Time Management Application

## Technologies and Versions
- PHP 8.4.5
- Symfony 7.2.4
- MySQL 8.4.4

## Configuration Parameters
The application uses the following configuration parameters, which can be modified in the config/services.yaml file:

```yaml
parameters:
    app.monthly_hours_norm: 40          # Monthly hours norm
    app.hourly_rate: 20.0               # Base hourly rate (PLN)
    app.overtime_rate_multiplier: 2.0   # Overtime rate multiplier (200%)
```

## API Documentation
You can test the API endpoints by clicking the button below to run the collection in Postman:

[<img src="https://run.pstmn.io/button.svg" alt="Run In Postman" style="width: 128px; height: 32px;">](https://god.gw.postman.com/run-collection/32708062-99de0029-31f0-4f8c-974c-8ed6db94b138?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D32708062-99de0029-31f0-4f8c-974c-8ed6db94b138%26entityType%3Dcollection%26workspaceId%3D12f60be8-d681-43c6-b630-cc0451a2b8ee)

## Available Endpoints

- **POST** `/api/employee/create` - Create a new employee with first name and surname
  ```json
  {
    "firstName": "Piotr",
    "surname": "Kapustka"
  }
  ```

- **POST** `/api/work-time/register` - Register work time for an employee
```json
{
  "employeeId": "f2ac3250-90b1-4e2f-9103-5522b4dc30b3",
  "startDateTime": "2025-03-27T08:00:00",
  "endDateTime": "2025-03-27T14:00:00"
}
```

- **GET** `/api/work-time/summary?employeeId={employeeId}&date={date}` - Get work time summary

Parameters:
    - employeeId: Employee's unique identifier
    - date: Date in format YYYY-MM-DD for daily summary or YYYY-MM for monthly summary

Enjoy!
