-- Vectors are normalized to length 1 at write time, so cosine similarity
-- collapses into a plain dot product. Runs on stock MySQL 8 (JSON_TABLE).
--
-- If your client refuses to create the function, run first:
--   SET GLOBAL log_bin_trust_function_creators = 1;

USE nimbus;

DROP FUNCTION IF EXISTS dot_product;

DELIMITER //

CREATE FUNCTION dot_product(a JSON, b JSON)
RETURNS DOUBLE
DETERMINISTIC
BEGIN
    DECLARE result DOUBLE;

    SELECT SUM(x.v * y.v)
      INTO result
      FROM JSON_TABLE(a, '$[*]'
             COLUMNS (i FOR ORDINALITY, v DOUBLE PATH '$')) AS x
      JOIN JSON_TABLE(b, '$[*]'
             COLUMNS (i FOR ORDINALITY, v DOUBLE PATH '$')) AS y
        ON x.i = y.i;

    RETURN result;
END //

DELIMITER ;
