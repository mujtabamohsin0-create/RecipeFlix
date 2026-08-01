-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2026 at 09:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `recipe_netflix`
--

-- --------------------------------------------------------

--
-- Table structure for table `mylist`
--

CREATE TABLE `mylist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `instructions` longtext DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `cooking_time` varchar(50) DEFAULT NULL,
  `difficulty` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `title`, `description`, `ingredients`, `instructions`, `category`, `image_url`, `cooking_time`, `difficulty`, `created_at`) VALUES
(1, 'Biryani', 'A flavorful Pakistani rice dish made with tender chicken, fragrant basmati rice, and a blend of aromatic spices, cooked together for a rich and satisfying meal.', 'Rice \r\nChicken \r\nOnion \r\nTomatoes\r\nYogurt\r\nGreen Chilles\r\n', '1. Wash and soak the basmati rice for 30 minutes, then boil until 70% cooked and set aside.\r\n\r\n2. Heat oil or ghee in a large pot and fry the sliced onions until golden brown. Remove half for garnishing.\r\n\r\n3. Add ginger-garlic paste and cook for 1 minute.\r\n\r\n4. Add the chicken and cook until it turns white.\r\n\r\n5. Mix in tomatoes, yogurt, biryani masala, turmeric, red chili powder, coriander powder, and salt. Cook until the chicken is tender and the oil separates.\r\n\r\n6. Add chopped green chilies, fresh coriander, and mint leaves.\r\n\r\n7. Spread the partially cooked rice over the chicken mixture.\r\n\r\n8. Sprinkle the fried onions, garam masala, saffron milk (optional), and a little ghee over the rice.\r\n\r\n9. Cover the pot tightly and cook on low heat (dum) for 20–25 minutes.\r\n\r\n10. Gently mix the layers before serving.\r\n\r\n11. Serve hot with raita, salad, or pickle.', 'Pakistani', 'img/biryani.jpg', '', 'Medium', '2026-07-25 12:43:59'),
(2, 'Biryani', 'A flavorful rice dish made with tender chicken, fragrant basmati rice, and a blend of aromatic spices, cooked together for a rich and satisfying meal.', 'Rice\r\nChicken\r\nOnion\r\nYogurt\r\nTomato ', '1. Wash and soak the basmati rice for 30 minutes, then boil until 70% cooked and set aside.\r\n\r\n2. Heat oil or ghee in a large pot and fry the sliced onions until golden brown. Remove half for garnishing.\r\n\r\n3. Add ginger-garlic paste and cook for 1 minute.\r\n\r\n4. Add the chicken and cook until it turns white.\r\n\r\n5. Mix in tomatoes, yogurt, biryani masala, turmeric, red chili powder, coriander powder, and salt. Cook until the chicken is tender and the oil separates.\r\n\r\n6. Add chopped green chilies, fresh coriander, and mint leaves.\r\n\r\n7. Spread the partially cooked rice over the chicken mixture.\r\n\r\n8. Sprinkle the fried onions, garam masala, saffron milk (optional), and a little ghee over the rice.\r\n\r\n9. Cover the pot tightly and cook on low heat (dum) for 20–25 minutes.\r\n\r\n10. Gently mix the layers before serving.\r\n\r\n11. Serve hot with raita, salad, or pickle.', 'Indian', 'img/biryani.jpg', '1hour', 'Medium', '2026-07-25 12:49:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-07-25 12:32:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mylist`
--
ALTER TABLE `mylist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mylist`
--
ALTER TABLE `mylist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mylist`
--
ALTER TABLE `mylist`
  ADD CONSTRAINT `mylist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mylist_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
