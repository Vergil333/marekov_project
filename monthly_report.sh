#!/bin/bash

usr="sds"
pwd="sds_pass"
db="sds"
query="SELECT id FROM customer WHERE measured = true;"

#SELECT SUM(CASE customer_input.opt1 WHEN 1 THEN device.s0_1_kw ELSE 0 END) + SUM(CASE customer_input.opt2 WHEN 1 THEN device.s0_2_kw ELSE 0 END) + SUM(CASE customer_input.opt3 WHEN 1 THEN device.s0_3_kw ELSE 0 END) AS total_kw FROM device INNER JOIN customer_input ON device.id=customer_input.device_id WHERE customer_input.customer_id=4;

# Read all customers and exclude inactive (unmeasured)
# Write result from sql query into variable result
mapfile result < <(mysql -u $usr -p$pwd $db -N -B -e "$query")
# If this is empty, scripts ends here. Nothing more to do. Goodbye
if [ -z "$result" ]; then exit 1; fi

# loop, for every returned result from db, do following
for row in "${result[@]}"
do
  # MySQL options
  # -s Silent
  # -N Skip column names
  # -B Batch output
  # -e Expression
  pwr_usage=`mysql -u $usr -p$pwd $db -s -N -B -e "SELECT IFNULL((SELECT SUM(CASE customer_input.opt1 WHEN 1 THEN device.s0_1_kw ELSE 0 END) + SUM(CASE customer_input.opt2 WHEN 1 THEN device.s0_2_kw ELSE 0 END) + SUM(CASE customer_input.opt3 WHEN 1 THEN device.s0_3_kw ELSE 0 END) FROM device INNER JOIN customer_input ON device.id=customer_input.device_id WHERE customer_input.customer_id=$row), 0);"`
  # pwr_usage = (sum of all values from SDS inputs assigned for specific customer - latest power usage record found) if subtract is negative then 0
  mysql -u $usr -p$pwd $db -s -N -B -e "INSERT INTO report (customer_id, power) VALUES ($row, $pwr_usage);"
done
