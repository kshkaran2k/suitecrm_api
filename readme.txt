suitecrmapi
* Insert(set_entry) or create relationship(set_relationship) between the modules with any number of fields
    in any module of suitecrm/sugarcrm from a csv file.

The script will take the module name and the modes(set_entry or set_relationship) from the csv file only.


Prerequisites/Config
*register_argc_argv should be "ON" in php.ini


Installing
Just download and start using


Test
I have only tested in the below mentioned conditions
*Ubuntu system with php 5.5-5.7
*Suitecrm 7.5
*Sugarcrm 6.5


How to run the script?
You can run the script using the below command
php suitecrmapi.php <your_csv_file.csv>
