<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('super_awesome_example_simple')};
CREATE TABLE {$this->getTable('super_awesome_example_simple')} (
`id` INT NOT NULL AUTO_INCREMENT ,
`description` VARCHAR( 100 ) NOT NULL ,
`value` DECIMAL(12,2) NOT NULL ,
`period` DATE NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM ;

INSERT INTO {$this->getTable('super_awesome_example_simple')} (`description`, `value`, `period`) values ('Example One Description', 10.00, '2011-02-01');
INSERT INTO {$this->getTable('super_awesome_example_simple')} (`description`, `value`, `period`) values ('Example Two Description', 12.50, '2011-02-15');
INSERT INTO {$this->getTable('super_awesome_example_simple')} (`description`, `value`, `period`) values ('Example Three Description', 5.35, '2011-03-01');
INSERT INTO {$this->getTable('super_awesome_example_simple')} (`description`, `value`, `period`) values ('Example Four Description', 7.67, '2011-03-04');
INSERT INTO {$this->getTable('super_awesome_example_simple')} (`description`, `value`, `period`) values ('Example Dupe', 1.23, '2011-03-01');
INSERT INTO {$this->getTable('super_awesome_example_simple')} (`description`, `value`, `period`) values ('Example Dupe', 2.34, '2011-03-02');
INSERT INTO {$this->getTable('super_awesome_example_simple')} (`description`, `value`, `period`) values ('Example Dupe', 3.45, '2011-03-03');

");

$installer->endSetup();