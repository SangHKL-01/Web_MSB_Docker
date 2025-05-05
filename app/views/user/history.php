<h2 class="text-xl font-semibold mb-4">Lịch Sử Mua Hàng</h2>

<?php if (empty($orders)): ?>
  <p>Bạn chưa có đơn hàng nào.</p>
<?php else: ?>
  <table class="w-full border">
    <thead>
      <tr class="bg-gray-100">
        <th class="p-2 border">Mã đơn</th>
        <th class="p-2 border">Ngày đặt</th>
        <th class="p-2 border">Tổng tiền</th>
        <th class="p-2 border">Trạng thái</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td class="p-2 border"><?= $order['order_id'] ?></td>
          <td class="p-2 border"><?= $order['order_date'] ?></td>
          <td class="p-2 border"><?= number_format($order['total_amount']) ?>đ</td>
          <td class="p-2 border"><?= $order['status'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>