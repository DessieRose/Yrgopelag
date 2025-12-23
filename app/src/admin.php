<?php

require (__DIR__ . '/autoload.php');

$roomPrices = '';

$starsActive = '';

$features = '';

$discounts = '';

?>

SELECT
  f.id,
  a.name AS activity,
  t.name AS tier,
  f.name AS feature,
  hf.price,
  hf.active
FROM features f
JOIN activities a ON a.id = f.activity_id
JOIN tiers t ON t.id = f.tier_id
JOIN hotel_features hf ON hf.feature_id = f.id
ORDER BY a.name, t.base_cost;

<!-- Toggle active -->
UPDATE hotel_features
SET active = :active
WHERE feature_id = :id;

<!-- Update price -->
UPDATE hotel_features
SET price = :price
WHERE feature_id = :id;