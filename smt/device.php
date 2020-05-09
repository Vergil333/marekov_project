<?php include 'include/main.php'; ?>
<?php
try {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("SELECT * FROM device WHERE id=?;");
  $stmt->execute([htmlspecialchars($_GET["id"])]);
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
?>

<?php template_header('Device') ?>

    <main>
      <h3 style="text-align:left;">Zariadenie ID: <?=$data["id"]; ?> <span style="float:right;">IP: <a href="//<?=$data['ip']; ?>"><?=$data["ip"]; ?></a></span></h3>
      <hr>
      <h4 style="margin-bottom: 0">Stav zariadenia</h4>

      <form id="devUpdate">
        <table style="text-align: left">
          <tr>
            <th>ID</th>
            <td><input type="hidden" id="devId" name="device_id" value="<?=$data['id']; ?>"><?=$data["id"] ?></td>
          </tr>
          <tr>
            <th>Názov</th>
            <td><input type="text" id="devName" name="device_name" value="<?=$data['name']; ?>"></td>
          </tr>
          <tr>
            <th>Uptime</th>
            <td><?php echo readableTime($data["uptime"]); ?></td>
          </tr>
          <tr>
            <th>IP</th>
            <td><input type="text" id="devIp" name="device_ip" value="<?=$data['ip']; ?>"></td>
          </tr>
          <tr>
            <th>MAC</th>
            <td><input type="text" id="devMac" name="device_mac" value="<?=$data['mac']; ?>"></td>
          </tr>
          <tr>
            <th>Firmware</th>
            <td><?=$data["fwver"]; ?></td>
          </tr>
          <tr>
            <th>Povolené</th>
            <td><input type="checkbox" id="devAct" name="device_active" <?php if ($data['active']) echo "checked"; ?>></td>
          </tr>
          <tr>
            <th>Stav</th>
            <td><?php echo ($data["failed"]) ? utf8_encode("&#x274c;") : utf8_encode("&#x2714;"); ?></td>
          </tr>
        </table>
        <input type="submit" value="Upraviť">
      </form>

      <hr>

      <h4 style="margin-bottom: 0">Stav počítadla</h4>

      <table style="text-align: left">
          <th></th>
          <th>Aktuálna spotreba</th>
          <th>Celková spotreba</th>
        </tr>
        <tr>
          <th>Optočlen 1</th>
          <td><?=$data["s0_1_kwh"]; ?> kWh</td>
          <td><?=$data["s0_1_kw"]; ?> kW</td>
        </tr>
        <tr>
          <th>Optočlen 2</th>
          <td><?=$data["s0_2_kwh"]; ?> kWh</td>
          <td><?=$data["s0_2_kw"]; ?> kW</td>
        </tr>
        <tr>
          <th>Optočlen 3</th>
          <td><?=$data["s0_3_kwh"]; ?> kWh</td>
          <td><?=$data["s0_3_kw"]; ?> kW</td>
        </tr>
      </table>

      <hr>

      <h4>Detaily</h4>
      <div id="snmpDiv"><button id="snmpBtn" value="<?=$data["ip"] ?>">Zobraziť</button></div>

      <hr>

      <h4>Grafy</h4>

      <button style="float:right;" id="devDelBtn" value="<?=$data["id"] ?>">Vymazať zariadenie</button>
    </main>
<?php template_footer() ?>
