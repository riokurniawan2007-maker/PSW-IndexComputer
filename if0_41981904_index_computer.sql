-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql205.infinityfree.com
-- Generation Time: Jun 02, 2026 at 09:01 AM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41981904_index_computer`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `name`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '2026-05-21 09:53:08');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `logo_url`, `is_active`) VALUES
(1, 'ASUS', NULL, 1),
(2, 'Lenovo', NULL, 1),
(3, 'Acer', NULL, 1),
(4, 'HP', NULL, 1),
(5, 'Dell', NULL, 1),
(6, 'MSI', NULL, 1),
(7, 'Logitech', NULL, 1),
(8, 'Razer', NULL, 1),
(9, 'Kingston', NULL, 1),
(10, 'HyperX', NULL, 1),
(11, 'Epson', NULL, 1),
(12, 'Samsung', NULL, 1),
(13, 'Gigabyte', NULL, 1),
(14, 'Cooler Master', NULL, 1),
(15, 'Toshiba', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`, `created_at`) VALUES
(1, 'Laptop', 'laptop', 'laptop', 'Laptop berbagai merek dan spesifikasi', '2026-05-21 09:53:08'),
(2, 'PC & Komputer', 'pc-komputer', 'monitor', 'Desktop PC dan komputer rakitan', '2026-05-21 09:53:08'),
(3, 'Aksesoris', 'aksesoris', 'mouse', 'Mouse, keyboard, headset, dan aksesoris lainnya', '2026-05-21 09:53:08'),
(4, 'Hardware & Spare Part', 'hardware', 'cpu', 'Komponen PC: SSD, RAM, VGA, motherboard, dll', '2026-05-21 09:53:08'),
(5, 'Printer & Tinta', 'printer', 'printer', 'Printer dan tinta original', '2026-05-21 09:53:08'),
(6, 'Monitor', 'monitor', 'display', 'Monitor berbagai ukuran dan resolusi', '2026-05-21 09:53:08'),
(7, 'Gaming', 'gaming', 'gamepad', 'Perangkat gaming spesialis', '2026-05-21 09:53:08');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'kelpin', 'anto@gmail.com', '123', 'Info Produk', 'halo', 'unread', '2026-05-21 11:15:15'),
(2, 'teguh imut', 'teguh@gmai.com', '081389085219', 'Info Produk', 'Hi rio', 'unread', '2026-05-21 13:46:51'),
(3, 'rio', 'riokurniawan2007@gmail.com', '12312', 'Harga &amp; Penawaran', 'ada', 'unread', '2026-05-26 12:03:07');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `specifications` text DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image_url` varchar(500) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `brand`, `description`, `specifications`, `price`, `stock`, `image_url`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'ASUS VivoBook 14 A1404Z Core i3-1215U 8GB 512GB', 'asus-vivobook-14-a1404z', 'ASUS', 'Laptop tipis ringan untuk pelajar dan kerja harian. Layar 14 inci FHD anti-glare.', 'Intel Core i3-1215U, RAM 8GB DDR4, SSD 512GB, Layar 14\" FHD, Windows 11 Home', '5799000.00', 10, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(2, 1, 'ASUS VivoBook 15 X1502ZA Core i5-1235U 16GB 512GB', 'asus-vivobook-15-x1502za', 'ASUS', 'Laptop produktivitas layar 15.6 inci dengan performa tinggi untuk multitasking.', 'Intel Core i5-1235U, RAM 16GB DDR4, SSD 512GB, Layar 15.6\" FHD, Windows 11 Home', '8499000.00', 8, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(3, 1, 'ASUS ROG Strix G15 Ryzen 7 6800H RTX 3060 16GB 512GB', 'asus-rog-strix-g15', 'ASUS', 'Laptop gaming kelas atas dengan GPU RTX 3060 dan layar 144Hz.', 'AMD Ryzen 7 6800H, RAM 16GB DDR5, SSD 512GB, RTX 3060 6GB, Layar 15.6\" FHD 144Hz', '17999000.00', 4, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(4, 1, 'Lenovo IdeaPad Slim 3 Core i3-1215U 8GB 256GB', 'lenovo-ideapad-slim3-i3', 'Lenovo', 'Laptop entry-level terjangkau untuk kebutuhan sehari-hari dan pelajar.', 'Intel Core i3-1215U, RAM 8GB DDR4, SSD 256GB, Layar 15.6\" FHD, Windows 11 Home', '4999000.00', 12, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(5, 1, 'Lenovo IdeaPad 5 Core i5-1235U 16GB 512GB', 'lenovo-ideapad-5-i5', 'Lenovo', 'Laptop mid-range dengan desain elegan dan performa andal untuk kerja profesional.', 'Intel Core i5-1235U, RAM 16GB DDR4, SSD 512GB, Layar 15.6\" FHD IPS, Windows 11 Home', '8999000.00', 9, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(6, 1, 'Acer Aspire 3 Core i3-N305 8GB 512GB', 'acer-aspire-3-i3-n305', 'Acer', 'Laptop harga terjangkau dengan performa cukup untuk tugas harian dan pelajar.', 'Intel Core i3-N305, RAM 8GB DDR4, SSD 512GB, Layar 15.6\" FHD, Windows 11 Home', '5499000.00', 11, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(7, 1, 'Acer Nitro 5 Ryzen 5 7535HS RTX 2050 8GB 512GB', 'acer-nitro-5-r5-rtx2050', 'Acer', 'Laptop gaming mid-range dengan GPU RTX 2050 dan refresh rate 144Hz.', 'AMD Ryzen 5 7535HS, RAM 8GB DDR5, SSD 512GB, RTX 2050 4GB, Layar 15.6\" FHD 144Hz', '9999000.00', 6, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(8, 1, 'HP 14s Ryzen 5 5500U 8GB 512GB', 'hp-14s-r5-5500u', 'HP', 'Laptop ringan dengan prosesor AMD Ryzen 5 untuk produktivitas sehari-hari.', 'AMD Ryzen 5 5500U, RAM 8GB DDR4, SSD 512GB, Layar 14\" FHD, Windows 11 Home', '7299000.00', 7, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(9, 1, 'Dell Inspiron 15 Core i5-1235U 8GB 512GB', 'dell-inspiron-15-i5', 'Dell', 'Laptop Dell berkualitas dengan build quality premium untuk profesional.', 'Intel Core i5-1235U, RAM 8GB DDR4, SSD 512GB, Layar 15.6\" FHD, Windows 11 Home', '9499000.00', 5, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(10, 1, 'MSI Modern 14 Core i5-1235U 8GB 512GB', 'msi-modern-14-i5', 'MSI', 'Laptop tipis berdesain modern dengan performa tinggi untuk profesional muda.', 'Intel Core i5-1235U, RAM 8GB DDR4, SSD 512GB, Layar 14\" FHD IPS, Windows 11 Home', '9799000.00', 4, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(11, 2, 'PC Rakitan Gaming Mid-Range RTX 3060', 'pc-rakitan-gaming-rtx3060', NULL, 'PC rakitan gaming performa tinggi siap pakai. Cocok untuk gaming 1080p ultra settings.', 'Intel Core i5-12400F, RAM 16GB DDR4, SSD 512GB, RTX 3060 12GB, Casing ATX, PSU 650W', '12500000.00', 3, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(12, 2, 'PC Rakitan Office Lengkap', 'pc-rakitan-office-lengkap', NULL, 'Paket PC lengkap untuk kebutuhan kantor dan pekerjaan sehari-hari.', 'Intel Core i3-12100, RAM 8GB DDR4, SSD 256GB, Monitor 21.5\" FHD, Keyboard + Mouse', '6500000.00', 5, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(13, 2, 'PC Rakitan Student Budget', 'pc-rakitan-student-budget', NULL, 'PC rakitan terjangkau untuk pelajar dan mahasiswa dengan performa cukup.', 'Intel Core i3-10100, RAM 8GB DDR4, HDD 1TB, Casing Mini ATX', '3800000.00', 7, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(14, 2, 'PC Rakitan Desain & Video Editing', 'pc-rakitan-desain-editing', NULL, 'PC rakitan bertenaga untuk kebutuhan desain grafis dan video editing profesional.', 'Intel Core i7-12700, RAM 32GB DDR4, SSD 1TB NVMe, RTX 3070 8GB, Casing Full Tower', '22000000.00', 2, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(15, 2, 'Mini PC Intel NUC Core i5', 'mini-pc-intel-nuc-i5', 'Intel', 'Mini PC compact untuk ruang terbatas, cocok untuk office dan HTPC.', 'Intel Core i5-1135G7, RAM 8GB, SSD 256GB, WiFi 6, Bluetooth 5.0', '5200000.00', 4, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(16, 3, 'Logitech G102 Gaming Mouse', 'logitech-g102', 'Logitech', 'Mouse gaming wired dengan sensor HERO 8K dan RGB. Ringan dan responsif.', 'Sensor HERO 8K, DPI 200-8000, RGB Lightsync, Kabel 2.1m, Berat 85g', '350000.00', 25, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(17, 3, 'Logitech MK235 Keyboard + Mouse Wireless', 'logitech-mk235-wireless', 'Logitech', 'Combo keyboard dan mouse wireless untuk produktivitas kantor sehari-hari.', 'Keyboard full-size, Mouse optical 1000 DPI, Wireless 2.4GHz, Baterai tahan 36 bulan', '320000.00', 18, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(18, 3, 'HyperX Cloud II Gaming Headset', 'hyperx-cloud-ii', 'HyperX', 'Headset gaming 7.1 surround dengan mikrofon noise cancelling berkualitas tinggi.', '7.1 Surround Sound, Driver 53mm, Mikrofon noise cancelling, Kompatibel PC/PS4/Xbox', '1850000.00', 10, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(19, 3, 'Razer BlackWidow V3 Mechanical Keyboard', 'razer-blackwidow-v3', 'Razer', 'Keyboard mechanical gaming dengan switch Razer Green dan RGB Chroma.', 'Switch Razer Green, RGB Chroma, Anti-ghosting, Media keys, USB passthrough', '1650000.00', 8, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(20, 3, 'Cooler Master MM310 Ultralight Mouse', 'cooler-master-mm310', 'Cooler Master', 'Mouse gaming ultralight dengan bobot hanya 45g dan sensor PixArt 3389.', 'Sensor PixArt 3389, DPI 400-16000, Bobot 45g, Honeycomb shell, Kabel braided', '450000.00', 15, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(21, 3, 'Logitech C920 HD Webcam 1080p', 'logitech-c920-webcam', 'Logitech', 'Webcam Full HD 1080p untuk video call, streaming, dan meeting online.', 'Resolusi 1080p/30fps, Autofocus, Stereo mikrofon, USB, Kompatibel Windows/Mac', '950000.00', 12, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(22, 3, 'Fantech HG15 Headset Gaming RGB', 'fantech-hg15-rgb', 'Fantech', 'Headset gaming budget dengan suara surround dan desain RGB yang keren.', 'Driver 50mm, Surround 7.1, RGB lighting, Mikrofon fleksibel, Jack 3.5mm + USB', '185000.00', 20, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(23, 4, 'Kingston SSD A400 240GB SATA', 'kingston-ssd-a400-240gb', 'Kingston', 'SSD SATA 2.5 inci untuk upgrade laptop atau PC. Jauh lebih cepat dari HDD.', 'Kapasitas 240GB, Interface SATA3, Read 500MB/s, Write 350MB/s, Form factor 2.5\"', '380000.00', 30, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(24, 4, 'Kingston SSD A400 480GB SATA', 'kingston-ssd-a400-480gb', 'Kingston', 'SSD SATA 480GB kapasitas lebih besar untuk kebutuhan storage lebih banyak.', 'Kapasitas 480GB, Interface SATA3, Read 500MB/s, Write 450MB/s, Form factor 2.5\"', '620000.00', 20, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(25, 4, 'Samsung SSD 870 EVO 1TB SATA', 'samsung-ssd-870-evo-1tb', 'Samsung', 'SSD premium Samsung dengan kapasitas 1TB dan performa tinggi untuk profesional.', 'Kapasitas 1TB, Interface SATA3, Read 560MB/s, Write 530MB/s, MLC V-NAND', '1350000.00', 10, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(26, 4, 'Kingston RAM DDR4 8GB 3200MHz', 'kingston-ram-ddr4-8gb', 'Kingston', 'RAM DDR4 8GB untuk upgrade performa laptop atau PC desktop.', 'Kapasitas 8GB, DDR4 3200MHz, CL22, Voltase 1.2V, Kompatibel Intel & AMD', '320000.00', 35, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(27, 4, 'Kingston RAM DDR4 16GB 3200MHz', 'kingston-ram-ddr4-16gb', 'Kingston', 'RAM DDR4 16GB untuk multitasking berat dan gaming performa tinggi.', 'Kapasitas 16GB, DDR4 3200MHz, CL22, Voltase 1.2V, Kompatibel Intel & AMD', '580000.00', 22, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(28, 4, 'Gigabyte H610M S2H Motherboard LGA1700', 'gigabyte-h610m-s2h', 'Gigabyte', 'Motherboard Micro-ATX untuk prosesor Intel Gen 12 dan 13 socket LGA1700.', 'Socket LGA1700, Chipset H610, DDR4, PCIe 4.0, M.2, USB 3.2, mATX', '1450000.00', 8, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(29, 4, 'Toshiba HDD 1TB Internal 3.5 SATA', 'toshiba-hdd-1tb-35', 'Toshiba', 'Hard disk internal 1TB untuk penyimpanan data kapasitas besar di PC desktop.', 'Kapasitas 1TB, Interface SATA3, 7200RPM, Cache 64MB, Form factor 3.5\"', '580000.00', 15, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(30, 4, 'Toshiba Canvio Basics 1TB HDD Eksternal', 'toshiba-canvio-1tb', 'Toshiba', 'HDD eksternal portable 1TB, plug and play tanpa perlu power adapter.', 'Kapasitas 1TB, USB 3.0, Portable, Plug and play, Kompatibel Windows/Mac', '780000.00', 18, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(31, 5, 'Epson L3210 EcoTank Print Scan Copy', 'epson-l3210-ecotank', 'Epson', 'Printer multifungsi dengan sistem tinta EcoTank. Hemat biaya cetak jangka panjang.', 'Print, Scan, Copy, Resolusi 5760x1440 dpi, USB, Tinta EcoTank 003', '1850000.00', 8, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(32, 5, 'Epson L5290 WiFi Print Scan Copy Fax', 'epson-l5290-wifi', 'Epson', 'Printer multifungsi WiFi dengan fitur lengkap termasuk fax untuk kebutuhan kantor.', 'Print Scan Copy Fax, WiFi, Ethernet, ADF, Tinta EcoTank 003', '2950000.00', 5, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(33, 5, 'Epson Tinta 003 Black Original', 'epson-tinta-003-black', 'Epson', 'Tinta hitam original Epson untuk printer seri L1110, L3110, L3210, L5190.', 'Warna Black, Volume 65ml, Original Epson, Cocok untuk L1110/L3110/L3210/L5190', '88000.00', 60, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(34, 5, 'Epson Tinta 003 Color Set CMY', 'epson-tinta-003-color-set', 'Epson', 'Paket tinta warna original Epson isi 3 botol Cyan, Magenta, Yellow.', 'Isi Cyan + Magenta + Yellow, Volume 65ml/botol, Original Epson', '265000.00', 40, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(35, 6, 'Samsung 24 inch FHD IPS 75Hz', 'samsung-24-fhd-ips-75hz', 'Samsung', 'Monitor 24 inci FHD IPS dengan refresh rate 75Hz. Cocok untuk kerja dan gaming casual.', '24 inci, Resolusi 1920x1080, Panel IPS, 75Hz, HDMI + VGA, VESA 75x75', '1750000.00', 10, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(36, 6, 'Samsung 27 inch FHD VA 75Hz', 'samsung-27-fhd-va-75hz', 'Samsung', 'Monitor 27 inci FHD VA dengan kontras tinggi dan layar luas untuk produktivitas.', '27 inci, Resolusi 1920x1080, Panel VA, 75Hz, HDMI + VGA', '2350000.00', 7, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(37, 6, 'MSI G274F 27 inch FHD IPS 180Hz Gaming', 'msi-g274f-27-180hz', 'MSI', 'Monitor gaming 27 inci dengan refresh rate tinggi 180Hz untuk gaming kompetitif.', '27 inci, Resolusi 1920x1080, Panel IPS, 180Hz, 1ms, HDMI 2.0 + DP 1.2, FreeSync', '3850000.00', 5, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(38, 7, 'Razer DeathAdder V3 Gaming Mouse', 'razer-deathadder-v3', 'Razer', 'Mouse gaming ergonomis premium dengan sensor Focus Pro 30K untuk akurasi maksimal.', 'Sensor Focus Pro 30K, DPI 100-30000, 90jt klik, Bobot 59g, Kabel SpeedFlex', '1150000.00', 12, NULL, 1, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(39, 7, 'HyperX Pulsefire Haste 2 Gaming Mouse', 'hyperx-pulsefire-haste-2', 'HyperX', 'Mouse gaming ultralight dengan bobot 53g dan sensor presisi tinggi.', 'Sensor HyperX 26K, DPI 400-26000, Bobot 53g, Honeycomb shell, Switches Optical', '850000.00', 15, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08'),
(40, 7, 'Cooler Master CK530 V2 TKL Mechanical', 'cooler-master-ck530-v2-tkl', 'Cooler Master', 'Keyboard mechanical TKL compact dengan switch Cherry MX dan RGB per-key.', 'TKL layout, Switch Cherry MX Red, RGB per-key, Aluminum top plate, USB-C', '1250000.00', 8, NULL, 0, 1, '2026-05-21 09:53:08', '2026-05-21 09:53:08');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_percent` decimal(5,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`id`, `title`, `description`, `discount_percent`, `start_date`, `end_date`, `is_active`, `created_at`) VALUES
(1, 'Deals of the Week', 'Diskon spesial untuk produk pilihan minggu ini!', '15.00', '2026-05-21', '2026-05-28', 1, '2026-05-21 09:53:08'),
(2, 'Lucky Draw Rp200rb', 'Belanja min. Rp200.000 berhak ikut lucky draw!', NULL, '2026-05-21', '2026-06-20', 1, '2026-05-21 09:53:08'),
(3, 'Promo Laptop Pelajar', 'Diskon khusus laptop untuk pelajar dan mahasiswa.', '10.00', '2026-05-21', '2026-06-04', 1, '2026-05-21 09:53:08');

-- --------------------------------------------------------

--
-- Table structure for table `solutions`
--

CREATE TABLE `solutions` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `target` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `solutions`
--

INSERT INTO `solutions` (`id`, `name`, `slug`, `target`, `description`, `image_url`, `is_active`, `created_at`) VALUES
(1, 'Gaming Setup', 'gaming-setup', 'Gamer', 'High performance PC untuk smooth gaming dan maksimal FPS', NULL, 1, '2026-05-21 09:53:08'),
(2, 'Office Setup', 'office-setup', 'Profesional', 'PC andal dan efisien untuk kerja harian dan produktivitas', NULL, 1, '2026-05-21 09:53:08'),
(3, 'Design & Editing', 'design-editing', 'Kreator & Desainer', 'Setup powerful untuk graphic design, video editing, dan pekerjaan kreatif', NULL, 1, '2026-05-21 09:53:08'),
(4, 'Student Setup', 'student-setup', 'Pelajar & Mahasiswa', 'PC terjangkau dan andal untuk belajar, tugas, dan kebutuhan sehari-hari', NULL, 1, '2026-05-21 09:53:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_product_category` (`category_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `solutions`
--
ALTER TABLE `solutions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `solutions`
--
ALTER TABLE `solutions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
