INSERT INTO `answers` (`id`, `content`, `question_id`, `created_at`) VALUES
(1, 'Convection currents in the mantle', 1, '2024-03-04 01:18:20'),
(2, 'Divergent boundary', 2, '2024-03-04 01:20:55'),
(3, 'True', 3, '2024-03-04 01:22:35'),
(4, 'Transform boundary', 4, '2024-03-04 01:23:42');

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
(12, 'Subduction zone', 4, '2024-03-04 01:23:42');

INSERT INTO `courses` (`id`, `name`, `tutor_id`, `created_at`) VALUES
(1, 'Geology 101', 1, '2024-03-04 01:15:57');

INSERT INTO `questions` (`id`, `type`, `content`, `quiz_id`, `created_at`) VALUES
(1, 'MC', 'What is the primary driving force behind plate tectonics?', 1, '2024-03-04 01:18:20'),
(2, 'MC', 'Which type of plate boundary is characterized by plates moving away from each other?', 1, '2024-03-04 01:20:55'),
(3, 'TF', 'The movement of tectonic plates is responsible for the formation of earthquakes and volcanic activity.', 1, '2024-03-04 01:22:35'),
(4, 'MC', 'What type of boundary is associated with the sliding past of two adjacent tectonic plates?', 1, '2024-03-04 01:23:42'),
(5, 'OE', 'Define a fault in the context of geology.', 1, '2024-03-04 01:25:17');

INSERT INTO `quizzes` (`id`, `name`, `code`, `course_id`, `created_at`) VALUES
(1, 'Plate Techtonics', '96866', 1, '2024-03-04 01:17:44');

INSERT INTO `tutors` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Emdadul', 'haquee1@montclair.edu', '$argon2i$v=19$m=65536,t=4,p=1$dnMvazVHZEoyWnE0NDJMSA$C0SDo+22raaug38gEi4gcE2p1ZxFS9BqfaDik1NVuew', '2024-03-04 00:27:05');
