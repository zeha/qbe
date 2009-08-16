CREATE VIEW get_stundenplan (stunde,beginn,ende,lehrer,fach,jg,klasse,Abteilung,WochenTag)
AS
SELECT
  stunde.id as stunde,
  stunde.beginn as beginn,
  stunde.ende as ende,
  lehrer."name" as lehrer,
  fach."name" AS fach,
  klasse.jg as jg,
  klasse."name" AS klasse,
  abteilung."name" AS Abteilung,
  stundenplan.wtag as WochenTag
FROM
  stundenplan
  INNER JOIN klasse ON (stundenplan.klasse = klasse.id)
  INNER JOIN abteilung ON (stundenplan.abteilung = abteilung.field1)
  INNER JOIN fach ON (stundenplan.fach = fach.id)
  INNER JOIN lehrer ON (stundenplan.lehrer = lehrer.id)
  INNER JOIN stunde ON (stundenplan.stunde = stunde.id);
