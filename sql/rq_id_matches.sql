SELECT * FROM
(
SELECT s.store_id, s.location, r.rq_store_id 
FROM stores s
INNER JOIN rq_stores r ON s.store_id = r.store_id
) rs
INNER JOIN
(
SELECT store_id, store_name
FROM rq_product_detail
GROUP BY store_id
) rd ON rs.rq_store_id = rd.store_id

SELECT * FROM
(
SELECT store_id, store_name
FROM rq_product_detail
GROUP BY store_id
) rd
LEFT JOIN
(
SELECT s.store_id, s.location, r.rq_store_id 
FROM stores s
INNER JOIN rq_stores r ON s.store_id = r.store_id
) rs ON rd.store_id = rs.rq_store_id

SELECT s.store_id, s.location, r.rq_store_id 
FROM stores s
LEFT JOIN rq_stores r ON s.store_id = r.store_id
HAVING r.rq_store_id IS NULL

Cherry Hill Mall
Cross Creek
Garden State Plaza
Lewis Center
Pearl Walmart
Reynoldsburg
Pelham Walmart
Bradenton Walmart
Plymouth Walmart
Cherry Hill Walmart
Hope Mills
Fayetteville
