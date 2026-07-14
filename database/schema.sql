CREATE DATABASE IF NOT EXISTS eugen_portfolio
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE eugen_portfolio;

CREATE TABLE IF NOT EXISTS skills (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category VARCHAR(80) NOT NULL,
  items VARCHAR(255) NOT NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_visible TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY skills_category_unique (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS projects (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(160) NOT NULL,
  project_type VARCHAR(80) NOT NULL,
  project_year YEAR NOT NULL,
  summary TEXT NOT NULL,
  technologies VARCHAR(255) NOT NULL,
  project_url VARCHAR(255) NULL,
  repo_url VARCHAR(255) NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  is_featured TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY projects_title_unique (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL,
  subject VARCHAR(160) NOT NULL,
  message TEXT NOT NULL,
  ip_address VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY contact_messages_created_at_index (created_at),
  KEY contact_messages_email_index (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO skills (category, items, sort_order, is_visible) VALUES
  ('Frontend', 'HTML, CSS, JavaScript, React.js, Next.js, responsive UI', 10, 1),
  ('Backend', 'PHP, Laravel, Node.js, NestJS, REST APIs, server logic', 20, 1),
  ('Database', 'MySQL, schema design, CRUD workflows, prepared statements', 30, 1),
  ('Workflow', 'Git, GitHub, GitLab, debugging, code reviews, deployment basics', 40, 1),
  ('TypeScript Stack', 'TypeScript, React.js, Next.js, Node.js, NestJS', 50, 1),
  ('Automation & APIs', 'n8n, API integrations, workflow automation, webhooks', 60, 1)
ON DUPLICATE KEY UPDATE
  items = VALUES(items),
  sort_order = VALUES(sort_order),
  is_visible = VALUES(is_visible);

INSERT INTO projects
  (title, project_type, project_year, summary, technologies, project_url, repo_url, sort_order, is_featured)
VALUES
  (
    'TryNest',
    'GitLab project',
    2026,
    'A GitLab-hosted application project that shows source control practice, project organization, and software development work available for review.',
    'GitLab, Application Development, Source Code',
    NULL,
    'https://gitlab.com/securetodolist2/trynest',
    5,
    1
  ),
  (
    'Inventory Management System',
    'Full-stack app',
    2026,
    'A Laravel-style inventory workflow for managing items, stock changes, searchable records, and admin actions with a MySQL-backed data model.',
    'Laravel, PHP, MySQL, CRUD, Admin UI',
    NULL,
    NULL,
    10,
    1
  ),
  (
    'React Next.js Dashboard',
    'Frontend system',
    2026,
    'A responsive dashboard interface focused on reusable UI sections, clean navigation, status cards, and fast page structure for web applications.',
    'React.js, Next.js, TypeScript, Responsive UI',
    NULL,
    NULL,
    20,
    1
  ),
  (
    'NestJS API Service',
    'Backend API',
    2026,
    'A TypeScript backend concept for structured API routes, request validation, service-layer logic, and database-ready application workflows.',
    'NestJS, Node.js, TypeScript, REST APIs',
    NULL,
    NULL,
    30,
    1
  ),
  (
    'n8n Automation Workflow',
    'Automation',
    2026,
    'A workflow automation concept for connecting APIs, handling webhook events, moving data between services, and reducing repeated manual tasks.',
    'n8n, Webhooks, API Integration, Automation',
    NULL,
    NULL,
    40,
    1
  )
ON DUPLICATE KEY UPDATE
  project_type = VALUES(project_type),
  project_year = VALUES(project_year),
  summary = VALUES(summary),
  technologies = VALUES(technologies),
  project_url = VALUES(project_url),
  repo_url = VALUES(repo_url),
  sort_order = VALUES(sort_order),
  is_featured = VALUES(is_featured);
