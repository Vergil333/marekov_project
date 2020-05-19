<?php include 'include/main.php'; ?>
<?php
try {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("SELECT active, id, ip, s0_1_kw, s0_1_kwh, s0_2_kw, s0_2_kwh, s0_3_kw, s0_3_kwh, failed FROM device;");
  $stmtSum = $conn->prepare("SELECT SUM(s0_1_kwh) + SUM(s0_2_kwh) + SUM(s0_3_kwh) AS total FROM device WHERE active=1;");
  $stmt->execute();
  $stmtSum->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $kwhSum = $stmtSum->fetch(PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
?>

<?php template_header('Home') ?>

    <main>
      <p>Aktuálna spotreba celého DC: <b><?=$kwhSum["total"]; ?> kWh</b></p>
      <p>Zariadenia označené ako &#x274c; sú deaktivované a nemonitorujú sa.</p>
      <p>Pri zariadeniach zvýraznených červenou farbou došlo k chybe pri komunikácii.</p>
      <table align="center" style="text-align: center; width: 80%">
        <tr>
          <th>Atívne</th>
          <th>IP</th>
          <th>Aktuálna spotreba [kWh]</th>
          <th>Celková spotreba [kW]</th>
        </tr>
<?php foreach ($data as $key=>$value): ?>
        <tr>
          <td rowspan="3" <?php if ($value["failed"] and $value["active"]): ?>class="bgWarning"<?php endif ?>><?php echo ($value["active"]) ? utf8_encode("&#x2714;") : utf8_encode("&#x274c;"); ?></td>
          <td rowspan="3" <?php if ($value["failed"] and $value["active"]): ?>class="bgWarning"<?php endif ?>><?php echo '<a href="device.php?id='.$value["id"].'">'.$value["ip"].'</a>'; ?></td>
          <td <?php if ($value["failed"] and $value["active"]): ?>class="bgWarning"<?php endif ?>><?=$value["s0_1_kwh"]; ?></td>
          <td <?php if ($value["failed"] and $value["active"]): ?>class="bgWarning"<?php endif ?>><?=$value["s0_1_kw"]; ?></td>
        </tr>
        <tr>
          <td <?php if ($value["failed"] and $value["active"]): ?>class="bgWarning"<?php endif ?>><?=$value["s0_2_kwh"]; ?></td>
          <td <?php if ($value["failed"] and $value["active"]): ?>class="bgWarning"<?php endif ?>><?=$value["s0_2_kw"]; ?></td>
        </tr>
        <tr>
          <td <?php if ($value["failed"] and $value["active"]): ?>class="bgWarning"<?php endif ?>><?=$value["s0_3_kwh"]; ?></td>
          <td <?php if ($value["failed"] and $value["active"]): ?>class="bgWarning"<?php endif ?>><?=$value["s0_3_kw"]; ?></td>
        </tr>
<?php endforeach; ?>
      </table>
    </main>
<?php template_footer() ?>
