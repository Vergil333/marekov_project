<?php
function pdo_connect_mysql() {
  $server = "localhost";
  $username = "sds";
  $password = "sds_pass";
  $database = "sds";

  try {
// ERROR Handling from the constructor is somehow not working
//    return new PDO("mysql:host=$server;dbname=$database", $username, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $conn = new PDO("mysql:host=$server;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
  }
  catch(PDOException $e) {
    return "ERROR: " . $e->getMessage();
  }
}

// This is SNMP request table constructor
if (isset($_GET['btn_snmp_dev_ip']) && !empty($_GET['btn_snmp_dev_ip'])) {
  exec('snmpwalk -Oqv -v 1 -c sdsxpublic '.$_GET["btn_snmp_dev_ip"].' 2>&1', $retArr);
  $desc = array("System Description", "System Object ID", "System Uptime", "System Contact", "System Name", "System Location", "System Services", "Interface Number", "Interface Index", "Interface Description", "Interface Type", "Interface MTU", "Interface Speed", "Interface Physical Address", "Interface Admin Status", "Interface Operation Status", "Interface Last Change", "Interface Rx Octets", "Interface Rx Unicast Packets", "Interface Rx Multicast Packets", "Interface Rx Discarded Packets", "Interface Rx Errors", "Interface Rx Unknown Protocols", "Interface Tx Octets", "Interface Tx Unicast Packets", "Interface Tx Multicast Packets", "Interface Tx Discarded Packets", "Interface Tx Errors", "Interface Tx Queue Length", "Interface MIB Specific", "IP Forwarding State", "IP Rx Packets", "IP Rx Header Errors", "IP Rx Address Errors", "IP Rx Unknown Protocols", "IP Rx Discarded Packets", "IP Tx Requests", "IP Address", "IP Index", "IP Network Mask", "Rx ICMP", "Rx ICMP Errors", "Tx ICMP", "Hardware System Uptime", "Hardware System Number of Users", "Hardware Device Index", "Hardware Device Type", "Hardware Device Description", "Hardware Processor ID", "Hardware Processor Load");
  echo '<table style="text-align: left">'."\n";
  foreach ($retArr as $retDesc => $retVal) {
    echo "<tr>\n";
    echo "<th>".$desc[$retDesc]."</th><td>".$retVal."</td>\n";
    echo "</tr>\n";
  }
  echo "</table>";
}

// This constructs tables and forms for customer input settings
if (isset($_GET['customer_id']) && !empty($_GET['customer_id'])) {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("SELECT customer_input.id, customer_input.device_id, device.ip, customer_input.opt1, customer_input.opt2, customer_input.opt3, customer_input.customer_id FROM customer_input INNER JOIN device ON customer_input.device_id=device.id WHERE customer_input.customer_id=?;");
  $stmtDev = $conn->prepare("SELECT id, ip FROM device WHERE id NOT IN (SELECT device_id FROM customer_input WHERE customer_id=?);");
  try {
    $stmt->execute([$_GET["customer_id"]]);
    $stmtDev->execute([$_GET["customer_id"]]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $dataDev = $stmtDev->fetchAll(PDO::FETCH_ASSOC);
  }
  catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage();
  }
// First form EDIT
  echo <<< EOT
<h4>Upraviť vstupy</h4>
<form id="cstmrInputDevUpdate">
  <table style="text-align: center">
    <tr>
      <th>ID</th>
      <th>SDS IP</th>
      <th>Optočlen 1</th>
      <th>Optočlen 2</th>
      <th>Optočlen 3</th>
    </tr>
EOT;
  foreach ($data as $key=>$value):
    echo "      <tr>\n";
    echo '        <td><input type="hidden" name="customer_input_id[]" value='.$value["id"].'>'.$value["id"]."</td>\n";
    echo "        <td>".$value["ip"]."</td>\n";
    echo '        <td><input type="checkbox" name="opt1[]" value="1" '.(($value["opt1"])?"checked":"").'></td>'."\n";
    echo '        <td><input type="checkbox" name="opt2[]" value="1" '.(($value["opt2"])?"checked":"").'></td>'."\n";
    echo '        <td><input type="checkbox" name="opt3[]" value="1" '.(($value["opt3"])?"checked":"").'></td>'."\n";
    echo "      </tr>\n";
  endforeach;
    echo '  </table>'."\n";
    echo '  <input type="submit" value="Uložiť">'."\n";
    echo '</form>'."\n";
// Second form ADD
  echo "<h4>Pridať zariadenie</h4>\n";
  echo '<form id="cstmrDevAdd">'."\n";
  echo '  <label for="cstmrDevSlct">SDS:</label>'."\n";
  echo '  <select id="cstmrDevSlct" name="customer_device_select_id">'."\n";
  foreach ($dataDev as $row):
    echo '    <option value='.$row["id"].'>'.$row["ip"].'</option>'."\n";
  endforeach;
  echo '  </select>'."\n";
  echo '  <label for="cstmrDevOpt1">Opt1:</label><input type="checkbox" id="cstmrDevOpt1" name="customer_device_opt1">'."\n";
  echo '  <label for="cstmrDevOpt2">Opt2:</label><input type="checkbox" id="cstmrDevOpt2" name="customer_device_opt2">'."\n";
  echo '  <label for="cstmrDevOpt3">Opt3:</label><input type="checkbox" id="cstmrDevOpt3" name="customer_device_opt3">'."\n";
  echo '  <input type="hidden" id="cstmrId" name="customer_id" value='.$_GET["customer_id"].'>'."\n";
  echo '  <input type="submit" value="Pridať">'."\n";
  echo '</form>'."\n";
// Third form DELETE
  echo "<h4>Odobrať zariadenie</h4>\n";
  echo '<form id="cstmrDevDel">'."\n";
  echo '  <label for="cstmrDevSlct">SDS:</label>'."\n";
  echo '  <select id="cstmrDevSlct" name="customer_device_select_id">'."\n";
  foreach ($data as $row):
    echo '    <option value='.$row["id"].'>'.$row["ip"].'</option>'."\n";
  endforeach;
  echo '  </select>'."\n";
  echo '  <input type="submit" value="Odstrániť">'."\n";
  echo '</form>'."\n";

  echo '<button style="float:right;" onClick="window.location.reload();">Zrušiť</button>'."\n<br>\n";
  $conn = null;
  exit;
}

// Device delete button
if (isset($_GET['btn_dev_del_id']) && !empty($_GET['btn_dev_del_id'])) {
  device_del($_GET['btn_dev_del_id']);
}

// Customer delete button
if (isset($_GET['btn_cstmr_del_id']) && !empty($_GET['btn_cstmr_del_id'])) {
  customer_del($_GET['btn_cstmr_del_id']);
}

// Customer device inputs update field
if (isset($_POST['customer_input_id']) && !empty($_POST['customer_input_id'])) {
  foreach ($_POST['customer_input_id'] as $key => $val) {
    $id = $val['value'];
    $opt1 = $_POST['opt'][0+$key*3][value];
    $opt2 = $_POST['opt'][1+$key*3][value];
    $opt3 = $_POST['opt'][2+$key*3][value];
    customer_device_update($id, $opt1, $opt2, $opt3);
  }
}

// This determine and call function to manipulate with database
if (isset($_GET['formId']) && !empty($_GET['formId'])) {
  $params = array();
  parse_str($_GET['formVal'], $params);
//print_r($params);

  switch ($_GET['formId']) {
    case 'cstmrAdd':
      $active = (!empty($params['customer_active'])) ? 1 : 0;
      if (!empty($params['customer_name'])) {customer_add($params['customer_name'], $active);} else {echo "CHYBA: Zlyhanie funkcie!";}
      break;
    case 'cstmrDel':
      if (!empty($params['customer_select_id'])) {customer_del($params['customer_select_id']);} else {echo "CHYBA: Zlyhanie funkcie!";}
      break;
    case 'cstmrUpdate':
      $active = (!empty($params['customer_measured'])) ? 1 : 0;
      if (!empty($params['customer_id']) && !empty($params['customer_name'])) {customer_update($params['customer_id'], $params['customer_name'], $active);} else {echo "CHYBA: Zlyhanie funkcie!";}
      break;
    case 'devAdd':
      $active = (!empty($params['device_active'])) ? 1 : 0;
      if (!empty($params['device_ip'])) {device_add($params['device_name'], $params['device_ip'], $active);} else {echo "CHYBA: Zlyhanie funkcie!";}
      break;
    case 'devDel':
      if (!empty($params['device_select_id'])) {device_del($params['device_select_id']);} else {echo "CHYBA: Zlyhanie funkcie!";}
      break;
    case 'devUpdate':
      $active = (!empty($params['device_active'])) ? 1 : 0;
      if (!empty($params['device_id']) && !empty($params['device_ip'])) {device_update($params['device_id'], $params['device_name'], $params['device_ip'], $params['device_mac'], $active);} else {echo "CHYBA: Zlyhanie funkcie!";}
      break;
    case 'cstmrDevAdd':
      $opt1 = (!empty($params['customer_device_opt1'])) ? 1 : NULL;
      $opt2 = (!empty($params['customer_device_opt2'])) ? 1 : NULL;
      $opt3 = (!empty($params['customer_device_opt3'])) ? 1 : NULL;
      if (!empty($params['customer_device_select_id'])) {customer_device_add($params['customer_device_select_id'], $opt1, $opt2, $opt3, $params['customer_id']);} else {echo "CHYBA: Zlyhanie funkcie!";}
      break;
    case 'cstmrDevDel':
      if (!empty($params['customer_device_select_id'])) {customer_device_del($params['customer_device_select_id']);} else {echo "CHYBA: Zlyhanie funkcie!";}
      break;
  }
}

function customer_add($name, $measured) {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("INSERT INTO customer (name, measured) VALUES (?, ?);");
  try {
    $stmt->execute([$name, $measured]);
    echo "Zákazník bol pridaný";
  }
  catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage();
  }
  $conn = null;
  exit;
}
function customer_del($id) {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("DELETE FROM customer WHERE id=?;");
  try {
    $stmt->execute([$id]);
    echo "Zákazník bol vymazaný";
  }
  catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage();
  }
  $conn = null;
  exit;
}
function customer_update($id, $name, $measured) {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("UPDATE customer SET name=?, measured=? WHERE id=?;");
  try {
    $stmt->execute([$name, $measured, $id]);
    echo "Zákazník bol aktualizovaný";
  }
  catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage();
  }
  $conn = null;
  exit;
}
function device_add($name, $ip, $active) {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("INSERT INTO device (name, ip, active) VALUES (?, ?, ?);");
  try {
    $stmt->execute([$name, $ip, $active]);
    echo "Zariadenie bolo pridané";
  }
  catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage();
  }
  $conn = null;
  exit;
}
function device_del($id) {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("DELETE FROM device WHERE id=?;");
  try {
    $stmt->execute([$id]);
    echo "Zariadenie bolo vymazané";
  }
  catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage();
  }
  $conn = null;
  exit;
}
function device_update($id, $name, $ip, $mac, $active) {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("UPDATE device SET name=?, ip=?, mac=?, active=? WHERE id=?;");
  try {
    $stmt->execute([$name, $ip, $mac, $active, $id]);
    echo "Zariadenie bolo aktualizované";
  }
  catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage();
  }
  $conn = null;
  exit;
}
function customer_device_add($dev_id, $opt1, $opt2, $opt3, $cstmr_id) {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("INSERT INTO customer_input (device_id, opt1, opt2, opt3, customer_id) VALUES (?, ?, ?, ?, ?);");
  try {
    $stmt->execute([$dev_id, $opt1, $opt2, $opt3, $cstmr_id]);
    echo "Zariadenie bolo pridelené";
  }
  catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage();
  }
  $conn = null;
  exit;
}
function customer_device_del($id) {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("DELETE FROM customer_input WHERE id=?;");
  try {
    $stmt->execute([$id]);
    echo "Zariadenie bolo odobrané";
  }
  catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage();
  }
  $conn = null;
  exit;
}
function customer_device_update($id, $opt1, $opt2, $opt3) {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("UPDATE customer_input SET opt1=?, opt2=?, opt3=? WHERE id=?;");
  try {
    $stmt->execute([$opt1, $opt2, $opt3, $id]);
    echo "ID: " . $id . " bolo aktualizované\n";
  }
  catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage();
  }
  $conn = null;
//  exit;
}
?>
