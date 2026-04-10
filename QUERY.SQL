use Ragon_Pruebas;
select * from OCRD where CardCode like 'p%'


SELECT 
    COLUMN_NAME AS [Columna], 
    DATA_TYPE AS [Tipo de Dato], 
    CHARACTER_MAXIMUM_LENGTH AS [Longitud (BD)],
    IS_NULLABLE AS [Permite Nulos]
FROM 
    INFORMATION_SCHEMA.COLUMNS 
WHERE 
    TABLE_NAME = 'OCRD' 
    AND COLUMN_NAME IN ('CardCode', 'CardName', 'LicTradNum', 'Cellular', 'E_mail')
ORDER BY 
    ORDINAL_POSITION;

select cardcode, cardname, LicTradNum, Cellular, E_mail from OCRD where CardCode like 'p%'

CardCode	nvarchar	15	NO
CardName	nvarchar	100	YES
LicTradNum	nvarchar	32	YES
Cellular	nvarchar	50	YES
E_Mail	nvarchar	100	YES

UPDATE archivos SET modulo = 'OC' WHERE modulo = 'INPUTS';

