#!/bin/bash

usr="sds"
pwd="sds_pass"
db="sds"
query="SELECT id, ip FROM device WHERE active = true;"

# Precitame z databazy zariadenia a neaktivne vynechame
# Vysledok zapiseme do pola result
mapfile result < <(mysql -u $usr -p$pwd $db -N -B -e "$query")
# Ak je result prazdne, tak script konci. Dovidenia
if [ -z "$result" ]; then exit 1; fi

# Prikazy nizsie sluzili len na debug
# Jedna sa o to, ze result da v jednom riadku id aj ip oddelene tabom \t
# Preto dany retazec (string) strihame aby sme s nim vedeli pracovat
#printf "%s\n" "${result[@]}"
#echo ${result[0]%$'\t'*}
#echo ${result[0]#*$'\t'}

# loop, pre kazdy vrateny vysledok (teda pocet vratenych riadkov) vykoname nasledujuce operacie
for row in "${result[@]}"
do
  # Prikaz nizsie sluzil len na debug.
  # echo ID:  ${row%$'\t'*} IP: ${row#*$'\t'}

  # foo pouzijeme len preto, ze neslo pracovat priamo s ${row#*$'\t'}
  # string ulozeny v foo obsahuje na konci whitespace. tr ho odstrani a zapiseme vysledok do premennej ip
  foo="${row#*$'\t'}"
  ip="$(echo -e "${foo}" | tr -d '[:space:]')"

  # Prikaz nam zgrepuje vysledok s konkretnymi hodnotami a zapise ich do premennej s0. Je tam viac grep vysledkov, vytvori pole s0[Num]
  s0=($(curl -m 1 -s http://${ip}/s0.xml | grep -oP '(?<=impT0>)[^<]+|(?<=text>)[^ kWh<]+|(?<=act>)[^ kW<]+|(?<=fwver>)[^<]+|(?<=uptime>)[^<]+'))

  # Nacitame si aktualne hodnoty pre prehladnost
  device_id="${row%$'\t'*}"
  fwver=${s0[0]}
  uptime=${s0[1]}
  opt1=${s0[2]}
  opt2=${s0[5]}
  opt3=${s0[8]}
  s0_1_kw=${s0[3]}
  s0_1_kwh=${s0[4]}
  s0_2_kw=${s0[6]}
  s0_2_kwh=${s0[7]}
  s0_3_kw=${s0[9]}
  s0_3_kwh=${s0[10]}

  # Ak je hodnota prazdna, tak poznacime do databazy zlyhane zariadenie a preskocime cyklus for
  if [ -z $opt1 ] || [ -z $opt2 ] || [ -z $opt3 ]
  then
    mysql -u $usr -p$pwd $db -N -B -e "UPDATE device SET failed=1, s0_1_kwh=0, s0_2_kwh=0, s0_3_kwh=0 WHERE id='$device_id';"
    continue
  fi

# MySQL options
# -s Silent
# -N Skip column names
# -B Batch output
# -e Expression

# Pripojime sa na databazu a vykoname set prikazov.
# Dane prikazy zacinaju hned od kraja dokumentu z 0 pozicie koli EOF
# V opacnom pripade by sme dostali chybu
mysql -u $usr -p$pwd $db -N -B << EOF
INSERT INTO value (device_id, opt1, opt2, opt3) VALUES ($device_id, $opt1, $opt2, $opt3);
UPDATE device SET fwver='$fwver', uptime='$uptime', s0_1_kw='$s0_1_kw', s0_1_kwh='$s0_1_kwh', s0_2_kw='$s0_2_kw', s0_2_kwh='$s0_2_kwh', s0_3_kw='$s0_3_kw', s0_3_kwh='$s0_3_kwh', failed=0 WHERE id='$device_id';
EOF

done
