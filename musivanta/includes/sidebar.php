<?php
$role = $currentUser['role'] ?? 'customer';
$items = [
    'admin' => [
        ['title' => 'Overview', 'link' => '/admin/dashboard.php'],
        ['title' => 'Users', 'link' => '/admin/users.php'],
        ['title' => 'Musicians', 'link' => '/admin/musicians.php'],
        ['title' => 'Bookings', 'link' => '/admin/bookings.php'],
        ['title' => 'Payments', 'link' => '/admin/payments.php'],
    ],
    'musician' => [
        ['title' => 'Dashboard', 'link' => '/musician/dashboard.php'],
        ['title' => 'Profile', 'link' => '/musician/profile.php'],
        ['title' => 'Availability', 'link' => '/musician/availability.php'],
        ['title' => 'Bookings', 'link' => '/musician/bookings.php'],
    ],
    'customer' => [
        ['title' => 'Dashboard', 'link' => '/customer/dashboard.php'],
        ['title' => 'Browse', 'link' => $appBasePath . '/customer/browse-musicians.php'],
        ['title' => 'My Bookings', 'link' => '/customer/my-bookings.php'],
        ['title' => 'Payments', 'link' => '/customer/payments.php'],
    ],
];
?>
<aside class="dashboard-sidebar">
    <div class="sidebar-brand">Musivanta</div>
    <ul>
        <?php foreach ($items[$role] as $item): ?>
            <li><a href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a></li>
        <?php endforeach; ?>
    </ul>
</aside>
