SET @storeToCopy = 3;

INSERT INTO stores (email, location, manager) VALUES
('ckelley@ifixandrepair.com', 'Chris\'s Test Store', 'Chris Kelley'),
('systems@ifixandrepair.com', 'Systems\'s Test Store', 'Danilo Avilan');

SELECT rq.invoice_id, rq.sku, rq.quantity, rq.store_id+1000, s.location, s.invoice_date, s.product_name, s.box_name
FROM stores s INNER JOIN rq_product_detail rq
WHERE rq.store_id = @storeToCopy
AND s.email = 'ckelley@ifixandrepair.com';

SELECT rq.invoice_id, rq.sku, rq.quantity, rq.store_id+2000, s.location, s.invoice_date, s.product_name, s.box_name
FROM stores s INNER JOIN rq_product_detail rq
WHERE rq.store_id = @storeToCopy
AND s.email = 'systems@ifixandrepair.com';

SELECT s.store_id, rq.rq_store_id
FROM stores s INNER JOIN rq_stores rq
WHERE rq_store_id = @storeToCopy
AND s.email = 'ckelley@ifixandrepair.com';

SELECT store_id, ship_date, ship_method, tracking_number
FROM store_boxes b INNER JOIN rq_stores rq ON b.store_id = rq.store_id
WHERE rq.store_id = @storeToCopy