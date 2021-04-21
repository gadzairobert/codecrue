-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 14, 2021 at 09:57 PM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 5.6.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pick`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `reports_all` ()  BEGIN
    CREATE TEMPORARY TABLE temp_salaries(months varchar(50) PRIMARY KEY, nett_salary DEC(10,2));
	CREATE TEMPORARY TABLE temp_purchases( months varchar(50) PRIMARY KEY,nett_purchases DEC(10,2));
	CREATE TEMPORARY TABLE temp_expenses( months varchar(50) PRIMARY KEY, nett_expenses DEC(10,2));
	CREATE TEMPORARY TABLE temp_sales( months varchar(50) PRIMARY KEY, nett_sales DEC(10,2));

	-- SALARIES
    INSERT INTO temp_salaries 
	SELECT DATE_FORMAT(pay_date, "%m-%Y") AS months, 
	SUM(net_salary) nett_salary FROM ipos.payrol
	GROUP BY DATE_FORMAT(pay_date, "%m-%Y"); 
	
	-- PURCHASES
	INSERT INTO temp_purchases 
	SELECT DATE_FORMAT(item_date, "%m-%Y") AS months, 
	SUM(net_price) nett_purchases FROM ipos.purchase_items 
	GROUP BY DATE_FORMAT(item_date, "%m-%Y"); 
	
	-- EXPENSES
	INSERT INTO temp_expenses 
	SELECT DATE_FORMAT(expense.expense_date, "%m-%Y") AS months, 
	SUM(expense_items.net_price) nett_expenses FROM ipos.expense 
	inner join expense_items 
	on expense.expense_id = expense_items.expense_id 
	GROUP BY DATE_FORMAT(expense.expense_date, "%m-%Y"); 
	
	-- SALES
	INSERT INTO temp_sales 
	SELECT DATE_FORMAT(inventory_order.inventory_order_created_date, "%m-%Y") months, 
	SUM(inventory_order_product.nett_price * 1.15) nett_sales
	FROM ipos.inventory_order_product 
	inner join ipos.inventory_order 
	on inventory_order.inventory_order_id = inventory_order_product.inventory_order_id 
	GROUP BY DATE_FORMAT(inventory_order.inventory_order_created_date, "%m-%Y");
	
	SELECT temp_salaries.months, temp_salaries.nett_salary, temp_purchases.nett_purchases, temp_expenses.nett_expenses, temp_sales.nett_sales
	from temp_salaries left outer join temp_purchases 
	on temp_salaries.months = temp_purchases.months
	left outer join temp_expenses on temp_expenses.months = temp_purchases.months
	left outer join temp_sales  on temp_expenses.months = temp_sales.months
	order by months asc;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_all_one` (IN `start_date` DATE, IN `end_date` DATE)  BEGIN
	
    CREATE TEMPORARY TABLE temp_salaries(months varchar(50) PRIMARY KEY, nett_salary DEC(10,2));
	CREATE TEMPORARY TABLE temp_purchases( months varchar(50) PRIMARY KEY,nett_purchases DEC(10,2));
	CREATE TEMPORARY TABLE temp_expenses( months varchar(50) PRIMARY KEY, nett_expenses DEC(10,2));
	CREATE TEMPORARY TABLE temp_sales( months varchar(50) PRIMARY KEY, nett_sales DEC(10,2));

	-- SALARIES
    INSERT INTO temp_salaries 
	SELECT DATE_FORMAT(pay_date, "%m-%Y") AS months, 
	SUM(net_salary) nett_salary FROM pick.payrol
	WHERE pay_date BETWEEN start_date and end_date
	GROUP BY DATE_FORMAT(pay_date, "%m-%Y"); 
	
	-- PURCHASES
	INSERT INTO temp_purchases 
	SELECT DATE_FORMAT(item_date, "%m-%Y") AS months, 
	SUM(net_price) nett_purchases FROM pick.purchase_items 
	WHERE item_date BETWEEN start_date and end_date
	GROUP BY DATE_FORMAT(item_date, "%m-%Y"); 
	
	-- EXPENSES
	INSERT INTO temp_expenses 
	SELECT DATE_FORMAT(expense.expense_date, "%m-%Y") AS months, 
	SUM(expense_items.net_price) nett_expenses FROM pick.expense 
	inner join expense_items 
	on expense.expense_id = expense_items.expense_id 
	WHERE expense.expense_date BETWEEN start_date and end_date
	GROUP BY DATE_FORMAT(expense.expense_date, "%m-%Y"); 
	
	-- SALES
	INSERT INTO temp_sales 
	SELECT DATE_FORMAT(inventory_order.inventory_order_created_date, "%m-%Y") months, 
	SUM(inventory_order_product.nett_price * 1.15) nett_sales
	FROM pick.inventory_order_product 
	inner join pick.inventory_order 
	on inventory_order.inventory_order_id = inventory_order_product.inventory_order_id 
	WHERE inventory_order.inventory_order_created_date BETWEEN start_date and end_date
	GROUP BY DATE_FORMAT(inventory_order.inventory_order_created_date, "%m-%Y");
	
	SELECT temp_salaries.months, temp_salaries.nett_salary, temp_purchases.nett_purchases, temp_expenses.nett_expenses, temp_sales.nett_sales, 
	(temp_salaries.nett_salary + temp_expenses.nett_expenses + temp_purchases.nett_purchases) total_cash_out, 
	(temp_sales.nett_sales - temp_purchases.nett_purchases) profit
	from temp_salaries left outer join temp_purchases 
	on temp_salaries.months = temp_purchases.months
	left outer join temp_expenses on temp_expenses.months = temp_purchases.months
	left outer join temp_sales  on temp_expenses.months = temp_sales.months
	order by months asc;

	DROP TEMPORARY TABLE temp_salaries;
	DROP TEMPORARY TABLE temp_purchases;
	DROP TEMPORARY TABLE temp_expenses;
	DROP TEMPORARY TABLE temp_sales;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `stock_update` (`inventory_order_purchase_id` INT)  BEGIN
	
	UPDATE purchase_items, inventory_order_product 
	SET purchase_items.gross_quantity = purchase_items.gross_quantity - inventory_order_product.quantity  
	WHERE purchase_items.inventory_order_purchase_id = inventory_order_purchase_id;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand_name` varchar(250) NOT NULL,
  `brand_status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(250) NOT NULL,
  `category_status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `category_status`) VALUES
(1, 'Metals', 'active'),
(15, 'Paper', 'active'),
(16, 'Plastic', 'active'),
(17, 'Pvc4', 'active'),
(18, 'Glass', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employee_id` int(10) NOT NULL,
  `employee_code` int(10) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone_no` int(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(250) NOT NULL,
  `dob` date NOT NULL,
  `id_no` varchar(20) NOT NULL,
  `nationality` varchar(100) NOT NULL,
  `roles` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `emp_type` varchar(100) NOT NULL,
  `employee_status` enum('Active','Inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`employee_id`, `employee_code`, `full_name`, `phone_no`, `email`, `address`, `dob`, `id_no`, `nationality`, `roles`, `start_date`, `emp_type`, `employee_status`) VALUES
(14, 91189, 'Robert Gadzai', 843564458, 'gadzairobert@gmail.com', '46 Loveday St, Selby, JHB', '2003-06-18', '9181918111289191', 'Zimbabwean', 'Supervisor', '2021-02-01', 'Contract', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `expense`
--

CREATE TABLE `expense` (
  `expense_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expense_total` double(10,2) NOT NULL,
  `expense_status` enum('active','inactive') NOT NULL,
  `expense_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `expense`
--

INSERT INTO `expense` (`expense_id`, `user_id`, `expense_total`, `expense_status`, `expense_date`) VALUES
(21, 1, 336.00, 'active', '2021-04-14'),
(22, 1, 36.00, 'active', '2021-04-14'),
(23, 1, 510572.00, 'active', '2021-04-14');

-- --------------------------------------------------------

--
-- Table structure for table `expense_items`
--

CREATE TABLE `expense_items` (
  `expense_items_id` int(11) NOT NULL,
  `expense_id` int(11) NOT NULL,
  `exp_id` int(11) NOT NULL,
  `expense_item` varchar(100) NOT NULL,
  `quantity` int(10) NOT NULL,
  `unit` enum('kg','litre','meter') NOT NULL,
  `unit_price` double(10,2) NOT NULL,
  `net_price` double(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `expense_items`
--

INSERT INTO `expense_items` (`expense_items_id`, `expense_id`, `exp_id`, `expense_item`, `quantity`, `unit`, `unit_price`, `net_price`) VALUES
(80, 21, 3, 'gloves', 10, 'kg', 12.00, 120.00),
(81, 21, 4, 'plastic bags', 12, 'kg', 9.00, 108.00),
(82, 21, 1, 'bond paper', 9, 'kg', 12.00, 108.00),
(83, 22, 2, '12test', 3, 'kg', 12.00, 36.00),
(84, 23, 4, 'test1', 1919, 'kg', 188.00, 360772.00),
(85, 23, 1, 'test2', 9198, 'kg', 12.00, 110376.00),
(86, 23, 4, 'test3', 1232, 'kg', 32.00, 39424.00);

-- --------------------------------------------------------

--
-- Table structure for table `exp_list`
--

CREATE TABLE `exp_list` (
  `exp_id` int(11) NOT NULL,
  `exp_name` varchar(100) NOT NULL,
  `exp_status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exp_list`
--

INSERT INTO `exp_list` (`exp_id`, `exp_name`, `exp_status`) VALUES
(1, 'stationary', 'active'),
(2, 'petroleum', 'active'),
(3, 'PPE', 'active'),
(4, 'refuse plastic', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_customer`
--

CREATE TABLE `inventory_customer` (
  `customer_id` int(11) NOT NULL,
  `inventory_order_name` varchar(255) NOT NULL,
  `inventory_order_address` varchar(255) NOT NULL,
  `phone_number` int(10) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `customer_status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inventory_customer`
--

INSERT INTO `inventory_customer` (`customer_id`, `inventory_order_name`, `inventory_order_address`, `phone_number`, `email_address`, `customer_status`) VALUES
(8, 'renewIt', '45 Ellof Street, JHB', 2147483647, 'renewit@gmail.com', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_order`
--

CREATE TABLE `inventory_order` (
  `inventory_order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `inventory_order_total` double(10,2) NOT NULL,
  `inventory_order_name` varchar(255) NOT NULL,
  `inventory_order_status` enum('active','inactive') NOT NULL,
  `inventory_order_created_date` date NOT NULL,
  `invoice_no` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inventory_order`
--

INSERT INTO `inventory_order` (`inventory_order_id`, `user_id`, `inventory_order_total`, `inventory_order_name`, `inventory_order_status`, `inventory_order_created_date`, `invoice_no`) VALUES
(5, 1, 206.45, 'renewIt', 'active', '2021-04-14', 'IN128'),
(6, 1, 1048.80, 'renewIt', 'active', '2021-04-14', 'INV122'),
(7, 1, 21.53, 'renewIt', 'active', '2021-04-14', 'INB922'),
(8, 1, 1829.88, 'renewIt', 'active', '2021-04-14', 'INV28182');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_order_product`
--

CREATE TABLE `inventory_order_product` (
  `inventory_order_product_id` int(11) NOT NULL,
  `inventory_order_purchase_id` int(11) NOT NULL,
  `inventory_order_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` double(10,2) NOT NULL,
  `uom` varchar(100) NOT NULL,
  `deduct` double(10,2) NOT NULL,
  `qty_nett` double(10,2) NOT NULL,
  `price` double(10,2) NOT NULL,
  `nett_price` double(10,2) NOT NULL,
  `tax` double(10,2) NOT NULL,
  `item_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inventory_order_product`
--

INSERT INTO `inventory_order_product` (`inventory_order_product_id`, `inventory_order_purchase_id`, `inventory_order_id`, `stock_id`, `product_id`, `quantity`, `uom`, `deduct`, `qty_nett`, `price`, `nett_price`, `tax`, `item_date`) VALUES
(7, 0, 5, 7, 0, 12.00, 'kg', 3.00, 0.00, 12.00, 0.00, 1.15, '2021-04-14'),
(8, 0, 5, 4, 0, 88.00, '%', 32.00, 59.84, 3.00, 179.52, 1.15, '2021-04-14'),
(9, 0, 6, 7, 0, 19.00, 'kg', 2.00, 0.00, 12.00, 0.00, 1.15, '2021-04-14'),
(10, 0, 6, 3, 0, 99.00, 'kg', 23.00, 76.00, 12.00, 912.00, 1.15, '2021-04-14'),
(11, 0, 7, 7, 0, 121.00, 'kg', 3.00, 0.00, 12.00, 0.00, 1.15, '2021-04-14'),
(12, 0, 7, 6, 0, 9.00, '%', 9.00, 8.19, 9.00, 73.71, 1.15, '2021-04-14'),
(13, 0, 7, 4, 0, 12.00, '%', 88.00, 1.44, 13.00, 18.72, 1.15, '2021-04-14'),
(14, 0, 8, 3, 0, 99.00, 'kg', 33.00, 0.00, 12.00, 0.00, 1.15, '2021-04-14'),
(15, 0, 8, 7, 0, 12.00, '%', 12.00, 10.56, 88.00, 929.28, 1.15, '2021-04-14'),
(16, 0, 8, 6, 0, 43.00, '%', 32.00, 29.24, 12.00, 350.88, 1.15, '2021-04-14'),
(17, 0, 8, 4, 0, 12.00, '%', 19.00, 9.72, 32.00, 311.04, 1.15, '2021-04-14');

-- --------------------------------------------------------

--
-- Table structure for table `payrol`
--

CREATE TABLE `payrol` (
  `pay_id` int(10) NOT NULL,
  `pay_date` date NOT NULL,
  `employee_code` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `address` varchar(250) NOT NULL,
  `start_date` date NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `branch_code` int(11) NOT NULL,
  `bank_account` int(20) NOT NULL,
  `account_type` varchar(100) NOT NULL,
  `rate_per_hour` double(10,2) NOT NULL,
  `basic_salary` double(10,2) NOT NULL,
  `loan_advance` double(10,2) NOT NULL,
  `over_time` double(10,2) NOT NULL,
  `uif` double(10,2) NOT NULL,
  `loan_repay` double(10,2) NOT NULL,
  `net_salary` double(10,2) NOT NULL,
  `pay_status` enum('Paid','Unpaid') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payrol`
--

INSERT INTO `payrol` (`pay_id`, `pay_date`, `employee_code`, `full_name`, `address`, `start_date`, `bank_name`, `branch_code`, `bank_account`, `account_type`, `rate_per_hour`, `basic_salary`, `loan_advance`, `over_time`, `uif`, `loan_repay`, `net_salary`, `pay_status`) VALUES
(32, '2021-04-30', 0, 'Robert Gadzai', '', '0000-00-00', 'Capitec Bank', 19118, 99182999, 'Savings', 12.00, 198229.00, 89.00, 0.00, 88.00, 21.00, 198209.00, 'Unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `product_name` varchar(300) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `product_unit` enum('kg','%') NOT NULL,
  `unit_price` double(10,2) NOT NULL,
  `product_base_price` double(10,2) NOT NULL,
  `product_tax` double(10,2) NOT NULL,
  `product_minimum_order` double(10,2) NOT NULL,
  `product_enter_by` int(11) NOT NULL,
  `product_status` enum('active','inactive') NOT NULL,
  `product_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `category_id`, `brand_id`, `product_name`, `product_quantity`, `product_unit`, `unit_price`, `product_base_price`, `product_tax`, `product_minimum_order`, `product_enter_by`, `product_status`, `product_date`) VALUES
(107, 15, 0, '766TEST', 123, 'kg', 8.00, 757.68, 94.71, 23.00, 1, 'inactive', '2021-03-06');

-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `inventory_purchase_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `inventory_purchase_total` double(10,2) NOT NULL,
  `inventory_purchase_status` enum('active','inactive') NOT NULL,
  `inventory_purchase_created_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `purchase`
--

INSERT INTO `purchase` (`inventory_purchase_id`, `user_id`, `inventory_purchase_total`, `inventory_purchase_status`, `inventory_purchase_created_date`) VALUES
(46, 1, 1791.72, 'active', '2021-04-14'),
(47, 1, 0.00, 'active', '2021-04-14'),
(48, 1, 10625.32, 'active', '2021-04-14');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `inventory_order_purchase_id` int(11) NOT NULL,
  `inventory_purchase_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(10) NOT NULL,
  `stock_id` int(10) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `gross_quantity` double(10,2) NOT NULL,
  `uom` varchar(100) NOT NULL,
  `deducted` double(10,2) NOT NULL,
  `net_quantity` double(10,2) NOT NULL,
  `unit_price` double(10,2) NOT NULL,
  `net_price` double(10,2) NOT NULL,
  `item_date` date NOT NULL,
  `item_status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`inventory_order_purchase_id`, `inventory_purchase_id`, `product_id`, `category_id`, `stock_id`, `item_name`, `gross_quantity`, `uom`, `deducted`, `net_quantity`, `unit_price`, `net_price`, `item_date`, `item_status`) VALUES
(58, 46, 0, 0, 3, '', 129.00, 'kg', 23.00, 0.00, 32.00, 0.00, '2021-04-14', 'active'),
(59, 46, 0, 0, 5, '', 91.00, 'kg', 3.00, 88.00, 22.00, 1936.00, '2021-04-14', 'active'),
(60, 46, 0, 0, 7, '', 88.00, 'kg', 8.00, 80.00, 9.00, 720.00, '2021-04-14', 'active'),
(61, 46, 0, 0, 6, '', 34.00, '%', 33.00, 22.78, 23.00, 523.94, '2021-04-14', 'active'),
(62, 46, 0, 0, 4, '', 189.00, '%', 21.00, 149.31, 12.00, 1791.72, '2021-04-14', 'active'),
(63, 47, 0, 0, 7, '', 133.00, '%', 12.00, 0.00, 21.00, 0.00, '2021-04-14', 'active'),
(64, 48, 0, 0, 3, '', 199.00, 'kg', 12.00, 0.00, 12.00, 0.00, '2021-04-14', 'active'),
(65, 48, 0, 0, 6, '', 123.00, 'kg', 43.00, 80.00, 32.00, 2560.00, '2021-04-14', 'active'),
(66, 48, 0, 0, 6, '', 321.00, 'kg', 12.00, 309.00, 23.00, 7107.00, '2021-04-14', 'active'),
(67, 48, 0, 0, 7, '', 33.00, '%', 12.00, 29.04, 33.00, 958.32, '2021-04-14', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `stock_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `stock_qty` int(11) NOT NULL,
  `item_status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`stock_id`, `item_name`, `stock_qty`, `item_status`) VALUES
(3, 'mix', 130, 'active'),
(4, 'steel', 77, 'active'),
(5, 'mix plastic', 91, 'active'),
(6, 'stahba', 105, 'active'),
(7, 'roberttest', 90, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `user_id` int(11) NOT NULL,
  `user_email` varchar(200) NOT NULL,
  `user_password` varchar(200) NOT NULL,
  `user_name` varchar(200) NOT NULL,
  `user_type` enum('master','user') NOT NULL,
  `user_status` enum('Active','Inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`user_id`, `user_email`, `user_password`, `user_name`, `user_type`, `user_status`) VALUES
(1, 'picknsellscrap@gmail.com', '$2y$10$T9UudJgPA42hgfQZftIJ6e7v/pPzwSz7CU6lnnry1zkmhVbpbSHCq', 'PnS Admin', 'master', 'Active'),
(11, 'robert@gmail.com', '$2y$10$9fnlOqF6cH4j53.A4XYIvOPPJKzNvGed9GoQOz1Y.cEiXnk7U6khG', 'roberttest', 'master', 'Active'),
(15, 'zzz@gmail.com', '$2y$10$.rA2qVps9xOpTtDe0IowlOoqUkNwZFMAWW/AdPzo4ahNxbVgpWUB2', 'robertnew', 'user', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `expense`
--
ALTER TABLE `expense`
  ADD PRIMARY KEY (`expense_id`);

--
-- Indexes for table `expense_items`
--
ALTER TABLE `expense_items`
  ADD PRIMARY KEY (`expense_items_id`);

--
-- Indexes for table `exp_list`
--
ALTER TABLE `exp_list`
  ADD PRIMARY KEY (`exp_id`);

--
-- Indexes for table `inventory_customer`
--
ALTER TABLE `inventory_customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `inventory_order`
--
ALTER TABLE `inventory_order`
  ADD PRIMARY KEY (`inventory_order_id`);

--
-- Indexes for table `inventory_order_product`
--
ALTER TABLE `inventory_order_product`
  ADD PRIMARY KEY (`inventory_order_product_id`);

--
-- Indexes for table `payrol`
--
ALTER TABLE `payrol`
  ADD PRIMARY KEY (`pay_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`inventory_purchase_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`inventory_order_purchase_id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`stock_id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `employee_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `expense`
--
ALTER TABLE `expense`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `expense_items`
--
ALTER TABLE `expense_items`
  MODIFY `expense_items_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `exp_list`
--
ALTER TABLE `exp_list`
  MODIFY `exp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inventory_customer`
--
ALTER TABLE `inventory_customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inventory_order`
--
ALTER TABLE `inventory_order`
  MODIFY `inventory_order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inventory_order_product`
--
ALTER TABLE `inventory_order_product`
  MODIFY `inventory_order_product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `payrol`
--
ALTER TABLE `payrol`
  MODIFY `pay_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `inventory_purchase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `inventory_order_purchase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
