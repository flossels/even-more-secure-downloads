#
# Table structure for table 'tx_evenmoresecuredownloads_claims'
#
CREATE TABLE tx_evenmoresecuredownloads_claims (
  hash varchar(255) DEFAULT '' NOT NULL,
  expires int(11) DEFAULT 0 NOT NULL,
  claimed int(11) DEFAULT 0 NOT NULL,
  user int(11) DEFAULT 0 NOT NULL,

  PRIMARY KEY(hash)
);
