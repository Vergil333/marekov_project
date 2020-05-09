<?php include 'include/main.php'; ?>
<?php
try {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("SELECT * FROM customer;");
  $stmtTotal = $conn->prepare("SELECT SUM(CASE customer_input.opt1 WHEN 1 THEN device.s0_1_kwh ELSE 0 END) + SUM(CASE customer_input.opt2 WHEN 1 THEN device.s0_2_kwh ELSE 0 END) + SUM(CASE customer_input.opt3 WHEN 1 THEN device.s0_3_kwh ELSE 0 END) AS total FROM device INNER JOIN customer_input ON device.id=customer_input.device_id;");
  $stmtCstmrUsage = $conn->prepare("SELECT SUM(CASE customer_input.opt1 WHEN 1 THEN device.s0_1_kwh ELSE 0 END) + SUM(CASE customer_input.opt2 WHEN 1 THEN device.s0_2_kwh ELSE 0 END) + SUM(CASE customer_input.opt3 WHEN 1 THEN device.s0_3_kwh ELSE 0 END) AS total FROM ((device INNER JOIN customer_input ON device.id=customer_input.device_id) INNER JOIN customer ON customer_input.customer_id=customer.id) WHERE customer.id=?;");
  $stmt->execute();
  $stmtTotal->execute();
//  $stmtCstmrUsage->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $dataTotal = $stmtTotal->fetch(PDO::FETCH_ASSOC);
//  $dataCstmrUsage = $stmtCstmrUsage->fetchAll(PDO::FETCH_ASSOC);
print_r($dataCstmrUsage);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
?>

<?php template_header('Customer') ?>

    <main>
      <p>Aktuálna spotreba všetkých zákazníkov: <b><?=$dataTotal["total"]; ?> kWh</b></p>
      <p>Zákazníci označení ako &#x274c; sa nemerajú.</p>
      <table align="center" style="text-align: center; width: 80%">
        <tr>
          <th>Názov</th>
          <th>Meraný</th>
          <th>Aktuálna spotreba [kWh]</th>
          <th>Spotreba za posledný mesiac [kW]</th>
        </tr>
<?php foreach ($data as $key=>$value): ?>
        <tr>
          <td><?php echo '<a href="customer.php?id='.$value["id"].'">'.$value["name"].'</a>'; ?></td>
          <td><?php echo ($value["measured"]) ? utf8_encode("&#x2714;") : utf8_encode("&#x274c;"); ?></td>
<?php
// This would be fine to do in one foreach loop and have $dataCstmrUsage as onetime run
  $stmtCstmrUsage->execute([$value["id"]]);
  $dataCstmrUsage = $stmtCstmrUsage->fetch(PDO::FETCH_ASSOC);
?>
          <td><?=$dataCstmrUsage["total"]; ?></td>
          <td>N/A</td>
        </tr>
<?php endforeach; ?>
      </table>
    </main>
<?php template_footer() ?>
