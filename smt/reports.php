<?php include 'include/main.php'; ?>
<?php
try {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("SELECT a.customer_id, customer.name, b.FromTime, a.ToTime, b.power - a.power AS difference FROM ((SELECT customer_id, MAX(timestamp) AS ToTime, power FROM report GROUP BY customer_id) a INNER JOIN (SELECT customer_id, MAX(timestamp) AS FromTime, power FROM report a WHERE (customer_id, timestamp) NOT IN (SELECT customer_id, MAX(timestamp) FROM report GROUP BY customer_id) GROUP BY customer_id, power HAVING FromTime = (SELECT MAX(timestamp) FROM report b WHERE a.customer_id=b.customer_id AND (b.customer_id, b.timestamp) NOT IN (SELECT customer_id, MAX(timestamp) FROM report GROUP BY customer_id) GROUP BY customer_id)) b ON b.customer_id=a.customer_id) INNER JOIN customer ON a.customer_id=customer.id;");
  $stmt->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
?>

<?php template_header('Reports') ?>

    <main>
      <p>Zobrazujú sa len meraní zákazníci.</p>
      <p>Ak sa mesačný report spustí mimo plánovaný termín, tak report nebude zobrazovať obdobie za celý mesiac. V tom prípade je potrebné spraviť manuálny prepočet z databázy!</p>
      <table align="center" style="text-align: center; width: 80%">
        <tr>
          <th>Názov</th>
          <th>Spotrebovaná energia [kW]</th>
          <th>Od</th>
          <th>Do</th>
        </tr>
<?php foreach ($data as $key=>$value): ?>
        <tr>
          <td><?php echo '<a href="customer.php?id='.$value["customer_id"].'">'.$value["name"].'</a>'; ?></td>
          <td><?=$value["difference"]; ?></td>
          <td><?=$value["FromTime"]; ?></td>
          <td><?=$value["ToTime"]; ?></td>
        </tr>
<?php endforeach; ?>
      </table>

      <hr>

      <h2>Historické záznamy</h2>
      <p>ToBeDone</p>
    </main>
<?php template_footer() ?>
