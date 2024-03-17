TRUNCATE `answers`;
TRUNCATE `choices`;
TRUNCATE `courses`;
TRUNCATE `questions`;
TRUNCATE `quizzes`;
TRUNCATE `responses`;
TRUNCATE `submissions`;
TRUNCATE `tutors`;

INSERT INTO `answers` (`id`, `content`, `question_id`, `created_at`) VALUES
(1, 'Convection currents in the mantle', 1, '2024-03-04 06:18:20'),
(2, 'Divergent boundary', 2, '2024-03-04 06:20:55'),
(3, 'True', 3, '2024-03-04 06:22:35'),
(4, 'Transform boundary', 4, '2024-03-04 06:23:42'),
(5, '2', 6, '2024-03-04 19:23:02'),
(6, '13', 7, '2024-03-04 19:24:17'),
(7, '11001', 8, '2024-03-04 19:25:22'),
(8, 'False', 9, '2024-03-04 19:26:02'),
(9, 'False', 11, '2024-03-04 16:34:30'),
(10, 'Resistance to scratching', 12, '2024-03-06 08:58:37'),
(11, 'Bauxite', 13, '2024-03-06 08:59:28'),
(12, 'Graphite', 14, '2024-03-06 09:00:25'),
(13, 'True', 15, '2024-03-06 09:01:11'),
(14, 'Calcite', 16, '2024-03-06 09:03:11'),
(15, 'Mars', 17, '2024-03-17 17:42:44'),
(16, 'Jupiter', 18, '2024-03-17 17:43:13'),
(17, 'Pluto', 19, '2024-03-17 17:43:44'),
(18, 'False', 20, '2024-03-17 17:46:00'),
(19, 'True', 21, '2024-03-17 17:46:51'),
(20, 'Leading', 22, '2024-03-17 17:50:13'),
(21, 'Kerning', 23, '2024-03-17 17:50:34'),
(22, 'Value', 24, '2024-03-17 18:05:43');

INSERT INTO `choices` (`id`, `content`, `question_id`, `created_at`) VALUES
(1, 'Magnetic forces', 1, '2024-03-04 06:18:20'),
(2, 'Gravitational forces', 1, '2024-03-04 06:18:20'),
(3, 'Radioactive decay', 1, '2024-03-04 06:18:20'),
(4, 'Convection currents in the mantle', 1, '2024-03-04 06:18:20'),
(5, 'Convergent boundary', 2, '2024-03-04 06:20:55'),
(6, 'Divergent boundary', 2, '2024-03-04 06:20:55'),
(7, 'Transform boundary', 2, '2024-03-04 06:20:55'),
(8, 'Subduction zone', 2, '2024-03-04 06:20:55'),
(9, 'Convergent boundary', 4, '2024-03-04 06:23:42'),
(10, 'Divergent boundary', 4, '2024-03-04 06:23:42'),
(11, 'Transform boundary', 4, '2024-03-04 06:23:42'),
(12, 'Subduction zone', 4, '2024-03-04 06:23:42'),
(13, '8', 6, '2024-03-04 19:23:02'),
(14, '10', 6, '2024-03-04 19:23:02'),
(15, '2', 6, '2024-03-04 19:23:02'),
(16, '16', 6, '2024-03-04 19:23:02'),
(17, '12', 7, '2024-03-04 19:24:17'),
(18, '13', 7, '2024-03-04 19:24:17'),
(19, '14', 7, '2024-03-04 19:24:17'),
(20, '15', 7, '2024-03-04 19:24:17'),
(21, '11001', 8, '2024-03-04 19:25:22'),
(22, '10011', 8, '2024-03-04 19:25:22'),
(23, '10101', 8, '2024-03-04 19:25:22'),
(24, '11010', 8, '2024-03-04 19:25:22'),
(25, 'Density', 12, '2024-03-06 08:58:37'),
(26, 'Resistance to scratching', 12, '2024-03-06 08:58:37'),
(27, 'Melting point', 12, '2024-03-06 08:58:37'),
(28, 'Transparency', 12, '2024-03-06 08:58:37'),
(29, 'Magnetite', 13, '2024-03-06 08:59:28'),
(30, 'Bauxite', 13, '2024-03-06 08:59:28'),
(31, 'Fluorite', 13, '2024-03-06 08:59:28'),
(32, 'Pyrite', 13, '2024-03-06 08:59:28'),
(33, 'Graphite', 14, '2024-03-06 09:00:25'),
(34, 'Sulfur', 14, '2024-03-06 09:00:25'),
(35, 'Talc', 14, '2024-03-06 09:00:25'),
(36, 'Mica', 14, '2024-03-06 09:00:25'),
(37, 'Quartz', 16, '2024-03-06 09:03:11'),
(38, 'Halite', 16, '2024-03-06 09:03:11'),
(39, 'Calcite', 16, '2024-03-06 09:03:11'),
(40, 'Magnetite', 16, '2024-03-06 09:03:11'),
(41, 'Venus', 17, '2024-03-17 17:42:44'),
(42, 'Jupiter', 17, '2024-03-17 17:42:44'),
(43, 'Mars', 17, '2024-03-17 17:42:44'),
(44, 'Saturn', 17, '2024-03-17 17:42:44'),
(45, 'Earth', 18, '2024-03-17 17:43:13'),
(46, 'Saturn', 18, '2024-03-17 17:43:13'),
(47, 'Uranus', 18, '2024-03-17 17:43:13'),
(48, 'Jupiter', 18, '2024-03-17 17:43:13'),
(49, 'Eris', 19, '2024-03-17 17:43:44'),
(50, 'Haumea', 19, '2024-03-17 17:43:44'),
(51, 'Makemake', 19, '2024-03-17 17:43:44'),
(52, 'Pluto', 19, '2024-03-17 17:43:44'),
(53, 'Kerning', 22, '2024-03-17 17:50:13'),
(54, 'Leading', 22, '2024-03-17 17:50:13'),
(55, 'Tracking', 22, '2024-03-17 17:50:13'),
(56, 'Alignment', 22, '2024-03-17 17:50:13'),
(57, 'Kerning', 23, '2024-03-17 17:50:34'),
(58, 'Ligature', 23, '2024-03-17 17:50:34'),
(59, 'Serif', 23, '2024-03-17 17:50:34'),
(60, 'Baseline', 23, '2024-03-17 17:50:34'),
(61, 'Hue', 24, '2024-03-17 18:05:43'),
(62, 'Saturation', 24, '2024-03-17 18:05:43'),
(63, 'Value', 24, '2024-03-17 18:05:43'),
(64, 'Tint', 24, '2024-03-17 18:05:43');

INSERT INTO `courses` (`id`, `name`, `tutor_id`, `created_at`) VALUES
(1, 'Foundations of Geology', 1, '2024-02-20 06:15:57'),
(2, 'Introduction to Computing', 1, '2024-02-25 15:34:52'),
(3, 'Graphic Design I', 1, '2024-03-01 16:07:07'),
(4, 'Basic Astronomy', 1, '2024-03-02 22:17:12');

INSERT INTO `questions` (`id`, `type`, `content`, `quiz_id`, `created_at`) VALUES
(1, 'MC', 'What is the primary driving force behind plate tectonics?', 1, '2024-03-04 06:18:20'),
(2, 'MC', 'Which type of plate boundary is characterized by plates moving away from each other?', 1, '2024-03-04 06:20:55'),
(3, 'TF', 'The movement of tectonic plates is responsible for the formation of earthquakes and volcanic activity.', 1, '2024-03-04 06:22:35'),
(4, 'MC', 'What type of boundary is associated with the sliding past of two adjacent tectonic plates?', 1, '2024-03-04 06:23:42'),
(5, 'OE', 'Define a fault in the context of geology.', 1, '2024-03-04 06:25:17'),
(6, 'MC', 'What is the base of the binary number system?', 2, '2024-03-04 19:23:02'),
(7, 'MC', 'What is the decimal equivalent of the binary number 1101?', 2, '2024-03-04 19:24:17'),
(8, 'MC', 'What is the binary representation of the decimal number 25?', 2, '2024-03-04 19:25:22'),
(9, 'TF', 'In the binary system, each digit is referred to as a \"byte\".', 2, '2024-03-04 19:26:02'),
(10, 'OE', 'What is the difference between serif fonts and sans-serif fonts?', 3, '2024-03-04 16:33:21'),
(11, 'TF', 'The choice of font in graphic design has little impact on the audience\'s perception of a message or brand.', 3, '2024-03-04 16:34:30'),
(12, 'MC', 'Mohs scale is a measure of a mineral\'s:', 4, '2024-03-06 08:58:37'),
(13, 'MC', 'Which mineral is a significant source of aluminum?', 4, '2024-03-06 08:59:28'),
(14, 'MC', 'What mineral is commonly found in both pencil \"lead\" and lubricants?', 4, '2024-03-06 09:00:25'),
(15, 'TF', 'The mineral diamond is composed of carbon atoms arranged in a crystal lattice structure.', 4, '2024-03-06 09:01:11'),
(16, 'MC', 'Which mineral is commonly associated with the formation of stalactites and stalagmites in caves?', 4, '2024-03-06 09:03:11'),
(17, 'MC', 'Which planet is known as the \"Red Planet\"?', 6, '2024-03-17 17:42:44'),
(18, 'MC', 'Which planet has the most moons?', 6, '2024-03-17 17:43:13'),
(19, 'MC', 'Which dwarf planet in our solar system was formerly considered the ninth planet until its reclassification in 2006?', 6, '2024-03-17 17:43:44'),
(20, 'TF', 'The asteroid belt lies between Jupiter and Saturn.', 6, '2024-03-17 17:46:00'),
(21, 'TF', 'Io is the only known celestial body in the solar system other than Earth where volcanic activity has been observed.', 6, '2024-03-17 17:46:51'),
(22, 'MC', 'What is the term used to describe the space between lines of text in typography?', 3, '2024-03-17 17:50:13'),
(23, 'MC', 'Which term describes the process of adjusting the spacing between pairs of letters in typography to improve visual appeal and readability?', 3, '2024-03-17 17:50:34'),
(24, 'MC', 'Which term in color theory describes the brightness or dullness of a color, often affected by adding black, white, or gray?', 5, '2024-03-17 18:05:43');

INSERT INTO `quizzes` (`id`, `name`, `code`, `course_id`, `created_at`) VALUES
(1, 'Plate Tectonics', '96866', 1, '2024-02-22 06:17:44'),
(2, 'Binary Numbers', '24128', 2, '2024-03-01 19:21:44'),
(3, 'Typography', '27516', 3, '2024-03-02 16:29:19'),
(4, 'Minerals', '12528', 1, '2024-03-10 16:29:35'),
(5, 'Color Theory', '42887', 3, '2024-03-12 10:12:05'),
(6, 'Our Solar System', '83596', 4, '2024-03-13 10:12:15');

INSERT INTO `responses` (`id`, `content`, `score`, `submission_id`, `question_id`, `created_at`) VALUES
(1, 'Convection currents in the mantle', 1, 1, 1, '2024-03-04 06:30:02'),
(2, 'Divergent boundary', 1, 1, 2, '2024-03-04 06:30:02'),
(3, 'True', 1, 1, 3, '2024-03-04 06:30:02'),
(4, 'Transform boundary', 1, 1, 4, '2024-03-04 06:30:02'),
(5, 'A fault is a zone of weakness in the Earth\'s crust where rocks on either side have moved relative to each other.', 1, 1, 5, '2024-03-04 06:30:02'),
(6, 'Convection currents in the mantle', 1, 2, 1, '2024-03-04 19:16:49'),
(7, 'Transform boundary', 0, 2, 2, '2024-03-04 19:16:49'),
(8, 'True', 1, 2, 3, '2024-03-04 19:16:49'),
(9, 'Convergent boundary', 0, 2, 4, '2024-03-04 19:16:49'),
(10, 'A break on the earth\'s surface.', 0.8, 2, 5, '2024-03-04 19:16:49'),
(11, '2', 1, 3, 6, '2024-03-04 16:36:36'),
(12, '13', 1, 3, 7, '2024-03-04 16:36:36'),
(13, '11010', 0, 3, 8, '2024-03-04 16:36:36'),
(14, 'False', 1, 3, 9, '2024-03-04 16:36:36'),
(15, 'Resistance to scratching', 1, 4, 12, '2024-03-06 09:08:09'),
(16, 'Magnetite', 0, 4, 13, '2024-03-06 09:08:09'),
(17, 'Graphite', 1, 4, 14, '2024-03-06 09:08:09'),
(18, 'True', 1, 4, 15, '2024-03-06 09:08:09'),
(19, 'Calcite', 1, 4, 16, '2024-03-06 09:08:09'),
(20, 'Serif fonts have tails at the ends of its characters while sans-serif fonts do not.', 0.5, 5, 10, '2024-03-17 17:54:44'),
(21, 'False', 1, 5, 11, '2024-03-17 17:54:44'),
(22, 'Tracking', 0, 5, 22, '2024-03-17 17:54:44'),
(23, 'Kerning', 1, 5, 23, '2024-03-17 17:54:44');

INSERT INTO `submissions` (`id`, `submitter`, `quiz_id`, `created_at`) VALUES
(1, 'Finnian', 1, '2024-02-23 06:30:02'),
(2, 'Caden', 1, '2024-03-05 19:16:49'),
(3, 'Harry', 2, '2024-03-09 16:36:36'),
(4, 'Landon', 4, '2024-03-10 09:08:09'),
(5, 'Finnian', 3, '2024-03-17 17:54:44');

INSERT INTO `tutors` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Emdadul', 'haquee1@montclair.edu', '$argon2i$v=19$m=65536,t=4,p=1$dnMvazVHZEoyWnE0NDJMSA$C0SDo+22raaug38gEi4gcE2p1ZxFS9BqfaDik1NVuew', '2024-02-04 05:27:05');
