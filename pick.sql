-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 21, 2021 at 04:59 PM
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
-- Table structure for table `bank_details`
--

CREATE TABLE `bank_details` (
  `employee_id` int(11) NOT NULL,
  `employee_code` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `branch_code` int(11) NOT NULL,
  `bank_account` int(11) NOT NULL,
  `account_type` varchar(100) NOT NULL,
  `account_status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bank_details`
--

INSERT INTO `bank_details` (`employee_id`, `employee_code`, `full_name`, `bank_name`, `branch_code`, `bank_account`, `account_type`, `account_status`) VALUES
(6, '2021', 'Khumbulani', 'NedBank', 2020, 89919991, 'Savings', 'active'),
(7, '91189', 'Robert Gadzai', 'ABSA Bank', 19191, 89101019, 'Savings', 'active'),
(8, '1999', 'Emily Nkosi', 'First National Bank SA', 29099, 2147483647, 'Cheque', 'active');

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
(14, 91189, 'Robert Gadzai', 843564458, 'gadzairobert@gmail.com', '46 Loveday St, Selby, JHB', '2003-06-18', 'cn99110', 'Zimbabwean', 'Supervisor', '2021-02-01', 'Contract', 'Active'),
(15, 2021, 'Khumbulani', 728911122, 'khumbu@gmail.com', '120 hwu', '2021-04-01', '2020192', 'SA', 'Supervisor', '2021-04-08', 'Contract', 'Active'),
(16, 1999, 'Emily Nkosi', 1992292929, 'g@gmail.com', '1299 belev', '2021-04-01', '19938210101', 'SA', 'Supervisor', '2021-04-01', 'General', 'Active');

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
(9, 'customer', '9229 hbje', 2147483647, 'test@gmail.com', 'active');

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
(31, 1, 185.15, 'customer', 'active', '2021-04-20', 'INV12');

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
(54, 0, 31, 12, 0, 29.00, 'kg', 22.00, 7.00, 23.00, 161.00, 1.15, '0000-00-00');

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
  `pay_status` enum('PAID','UNPAID') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payrol`
--

INSERT INTO `payrol` (`pay_id`, `pay_date`, `employee_code`, `full_name`, `address`, `start_date`, `bank_name`, `branch_code`, `bank_account`, `account_type`, `rate_per_hour`, `basic_salary`, `loan_advance`, `over_time`, `uif`, `loan_repay`, `net_salary`, `pay_status`) VALUES
(37, '2021-04-22', 0, 'Robert Gadzai', '', '0000-00-00', '', 0, 0, '', 91.00, 10000.00, 91.00, 0.00, 12.00, 8.00, 10071.00, 'PAID'),
(38, '2021-04-22', 0, 'Khumbulani', '', '0000-00-00', '', 0, 0, '', 188.00, 1999.00, 89.00, 0.00, 899.00, 12.00, 1177.00, 'PAID'),
(39, '2021-04-28', 0, 'Emily Nkosi', '', '0000-00-00', '', 0, 0, '', 81.92, 290119.00, 819.00, 0.00, 12.00, 89.00, 290837.00, 'PAID');

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
  `agent_name` varchar(100) NOT NULL,
  `inventory_purchase_total` double(10,2) NOT NULL,
  `inventory_purchase_status` enum('UNPAID','PAID') NOT NULL,
  `inventory_purchase_created_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `purchase`
--

INSERT INTO `purchase` (`inventory_purchase_id`, `user_id`, `agent_name`, `inventory_purchase_total`, `inventory_purchase_status`, `inventory_purchase_created_date`) VALUES
(72, 1, 'agent tesing ', 2093.00, 'PAID', '2021-04-20');

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
(112, 72, 0, 0, 12, '', 100.00, '%', 9.00, 91.00, 23.00, 2093.00, '2021-04-20', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `sale_agent`
--

CREATE TABLE `sale_agent` (
  `agent_id` int(11) NOT NULL,
  `agent_name` varchar(100) NOT NULL,
  `id_no` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `cell_no` int(11) NOT NULL,
  `agent_status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sale_agent`
--

INSERT INTO `sale_agent` (`agent_id`, `agent_name`, `id_no`, `address`, `cell_no`, `agent_status`) VALUES
(4, 'agent tesing ', '02929991911', 'test addrss ', 2147483647, 'active');

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
(12, 'steel', 71, 'active');

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
-- Indexes for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD PRIMARY KEY (`employee_id`);

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
-- Indexes for table `sale_agent`
--
ALTER TABLE `sale_agent`
  ADD PRIMARY KEY (`agent_id`);

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
-- AUTO_INCREMENT for table `bank_details`
--
ALTER TABLE `bank_details`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `employee_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `expense`
--
ALTER TABLE `expense`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `expense_items`
--
ALTER TABLE `expense_items`
  MODIFY `expense_items_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `exp_list`
--
ALTER TABLE `exp_list`
  MODIFY `exp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inventory_customer`
--
ALTER TABLE `inventory_customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inventory_order`
--
ALTER TABLE `inventory_order`
  MODIFY `inventory_order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `inventory_order_product`
--
ALTER TABLE `inventory_order_product`
  MODIFY `inventory_order_product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `payrol`
--
ALTER TABLE `payrol`
  MODIFY `pay_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `inventory_purchase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `inventory_order_purchase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `sale_agent`
--
ALTER TABLE `sale_agent`
  MODIFY `agent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
