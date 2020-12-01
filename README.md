# exchange_rates

Install via local server

1. Install local server (MAMP, Denver, OpenServer, etc.) with PHP 7.2, Apache, MySQL

2. Get project by run command (put it in destination domain folder)
```bash
git clone https://github.com/olgakopylova/exchange_rates.git
```

4. Change DB connection param to your own in file class/DB.php (lines 5-7)

5. Init DB by run command
```bash
php init_db.php
```

6. Run CRON task according to the selected local server.
For example in OpenServer:
Settings->Task Scheduler
```bash
0 0 * * *
%YOUR_PATH%modules\wget\bin\wget.exe -q --no-cache http://localhost/exchange_rates/update_script.php
```

6. Open browser and go to domainname.local (as example) 
