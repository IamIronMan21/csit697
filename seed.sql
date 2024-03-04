INSERT INTO `answers` (`id`, `content`, `question_id`, `created_at`) VALUES
(1, 'Convection currents in the mantle', 1, '2024-03-04 01:18:20'),
(2, 'Divergent boundary', 2, '2024-03-04 01:20:55'),
(3, 'True', 3, '2024-03-04 01:22:35'),
(4, 'Transform boundary', 4, '2024-03-04 01:23:42'),
(5, '2', 6, '2024-03-04 14:23:02'),
(6, '13', 7, '2024-03-04 14:24:17'),
(7, '11001', 8, '2024-03-04 14:25:22'),
(8, 'False', 9, '2024-03-04 14:26:02');

INSERT INTO `choices` (`id`, `content`, `question_id`, `created_at`) VALUES
(1, 'Magnetic forces', 1, '2024-03-04 01:18:20'),
(2, 'Gravitational forces', 1, '2024-03-04 01:18:20'),
(3, 'Radioactive decay', 1, '2024-03-04 01:18:20'),
(4, 'Convection currents in the mantle', 1, '2024-03-04 01:18:20'),
(5, 'Convergent boundary', 2, '2024-03-04 01:20:55'),
(6, 'Divergent boundary', 2, '2024-03-04 01:20:55'),
(7, 'Transform boundary', 2, '2024-03-04 01:20:55'),
(8, 'Subduction zone', 2, '2024-03-04 01:20:55'),
(9, 'Convergent boundary', 4, '2024-03-04 01:23:42'),
(10, 'Divergent boundary', 4, '2024-03-04 01:23:42'),
(11, 'Transform boundary', 4, '2024-03-04 01:23:42'),
(12, 'Subduction zone', 4, '2024-03-04 01:23:42'),
(13, '8', 6, '2024-03-04 14:23:02'),
(14, '10', 6, '2024-03-04 14:23:02'),
(15, '2', 6, '2024-03-04 14:23:02'),
(16, '16', 6, '2024-03-04 14:23:02'),
(17, '12', 7, '2024-03-04 14:24:17'),
(18, '13', 7, '2024-03-04 14:24:17'),
(19, '14', 7, '2024-03-04 14:24:17'),
(20, '15', 7, '2024-03-04 14:24:17'),
(21, '11001', 8, '2024-03-04 14:25:22'),
(22, '10011', 8, '2024-03-04 14:25:22'),
(23, '10101', 8, '2024-03-04 14:25:22'),
(24, '11010', 8, '2024-03-04 14:25:22');

INSERT INTO `courses` (`id`, `name`, `tutor_id`, `created_at`) VALUES
(1, 'Geology 101', 1, '2024-03-04 01:15:57'),
(2, 'Intro to Computer Science', 1, '2024-03-04 10:34:52');

INSERT INTO `questions` (`id`, `type`, `content`, `quiz_id`, `created_at`) VALUES
(1, 'MC', 'What is the primary driving force behind plate tectonics?', 1, '2024-03-04 01:18:20'),
(2, 'MC', 'Which type of plate boundary is characterized by plates moving away from each other?', 1, '2024-03-04 01:20:55'),
(3, 'TF', 'The movement of tectonic plates is responsible for the formation of earthquakes and volcanic activity.', 1, '2024-03-04 01:22:35'),
(4, 'MC', 'What type of boundary is associated with the sliding past of two adjacent tectonic plates?', 1, '2024-03-04 01:23:42'),
(5, 'OE', 'Define a fault in the context of geology.', 1, '2024-03-04 01:25:17'),
(6, 'MC', 'What is the base of the binary number system?', 2, '2024-03-04 14:23:02'),
(7, 'MC', 'What is the decimal equivalent of the binary number 1101?', 2, '2024-03-04 14:24:17'),
(8, 'MC', 'What is the binary representation of the decimal number 25?', 2, '2024-03-04 14:25:22'),
(9, 'TF', 'In the binary system, each digit is referred to as a \"byte.\"', 2, '2024-03-04 14:26:02');

INSERT INTO `quizzes` (`id`, `name`, `code`, `course_id`, `created_at`) VALUES
(1, 'Plate Techtonics', '96866', 1, '2024-03-04 01:17:44'),
(2, 'Binary Numbers', '24128', 2, '2024-03-04 14:21:44');

INSERT INTO `responses` (`id`, `content`, `score`, `submission_id`, `question_id`, `created_at`) VALUES
(1, 'Convection currents in the mantle', 1, 1, 1, '2024-03-04 01:30:02'),
(2, 'Divergent boundary', 1, 1, 2, '2024-03-04 01:30:02'),
(3, 'True', 1, 1, 3, '2024-03-04 01:30:02'),
(4, 'Transform boundary', 1, 1, 4, '2024-03-04 01:30:02'),
(5, 'A fault is a zone of weakness in the Earth\'s crust where rocks on either side have moved relative to each other.', 1, 1, 5, '2024-03-04 01:30:02'),
(6, 'Convection currents in the mantle', 1, 2, 1, '2024-03-04 14:16:49'),
(7, 'Transform boundary', 0, 2, 2, '2024-03-04 14:16:49'),
(8, 'True', 1, 2, 3, '2024-03-04 14:16:49'),
(9, 'Convergent boundary', 0, 2, 4, '2024-03-04 14:16:49'),
(10, 'A break on the earth\'s surface.', 0.8, 2, 5, '2024-03-04 14:16:49');

INSERT INTO `submissions` (`id`, `submitter`, `quiz_id`, `created_at`) VALUES
(1, 'Finnian', 1, '2024-03-04 01:30:02'),
(2, 'Caden', 1, '2024-03-04 14:16:49');

INSERT INTO `tutors` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Emdadul', 'haquee1@montclair.edu', '$argon2i$v=19$m=65536,t=4,p=1$dnMvazVHZEoyWnE0NDJMSA$C0SDo+22raaug38gEi4gcE2p1ZxFS9BqfaDik1NVuew', '2024-03-04 00:27:05');
