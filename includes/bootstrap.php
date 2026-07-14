<?php
declare(strict_types=1);

function portfolio_config(): array
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/../config.php';
    }

    return $config;
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function display_url(string $url): string
{
    return preg_replace('#^https?://#', '', rtrim($url, '/')) ?: $url;
}

function portfolio_db(): ?PDO
{
    static $pdo = null;
    static $loaded = false;

    if ($loaded) {
        return $pdo;
    }

    $loaded = true;
    $db = portfolio_config()['db'];
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $db['host'],
        $db['port'],
        $db['name'],
        $db['charset']
    );

    try {
        $pdo = new PDO($dsn, $db['user'], $db['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (Throwable $error) {
        error_log('Portfolio database connection failed: ' . $error->getMessage());
        $pdo = null;
    }

    return $pdo;
}

function fallback_skills(): array
{
    return array_merge(base_skills(), pinned_skills());
}

function base_skills(): array
{
    return [
        [
            'category' => 'Frontend',
            'items' => 'HTML, CSS, JavaScript, React.js, Next.js, responsive UI',
        ],
        [
            'category' => 'Backend',
            'items' => 'PHP, Laravel, Node.js, NestJS, REST APIs, server logic',
        ],
        [
            'category' => 'Database',
            'items' => 'MySQL, schema design, CRUD workflows, prepared statements',
        ],
        [
            'category' => 'Workflow',
            'items' => 'Git, GitHub, GitLab, debugging, code reviews, deployment basics',
        ],
    ];
}

function pinned_skills(): array
{
    return [
        [
            'category' => 'TypeScript Stack',
            'items' => 'TypeScript, React.js, Next.js, Node.js, NestJS',
        ],
        [
            'category' => 'Automation & APIs',
            'items' => 'n8n, API integrations, workflow automation, webhooks',
        ],
    ];
}

function with_pinned_skills(array $skills): array
{
    $existingCategories = array_map(
        fn (array $skill): string => strtolower((string) ($skill['category'] ?? '')),
        $skills
    );

    $missing = array_filter(
        pinned_skills(),
        fn (array $skill): bool => !in_array(strtolower($skill['category']), $existingCategories, true)
    );

    return array_merge($skills, $missing);
}

function pinned_projects(): array
{
    return [
        [
            'title' => 'TryNest',
            'project_type' => 'GitLab project',
            'project_year' => 2026,
            'summary' => 'A GitLab-hosted application project that shows source control practice, project organization, and software development work available for review.',
            'technologies' => 'GitLab, Application Development, Source Code',
            'project_url' => '',
            'repo_url' => 'https://gitlab.com/securetodolist2/trynest',
        ],
    ];
}

function fallback_projects(): array
{
    return array_merge(pinned_projects(), [
        [
            'title' => 'Inventory Management System',
            'project_type' => 'Full-stack app',
            'project_year' => 2026,
            'summary' => 'A Laravel-style inventory workflow for managing items, stock changes, searchable records, and admin actions with a MySQL-backed data model.',
            'technologies' => 'Laravel, PHP, MySQL, CRUD, Admin UI',
            'project_url' => '',
            'repo_url' => '',
        ],
        [
            'title' => 'React Next.js Dashboard',
            'project_type' => 'Frontend system',
            'project_year' => 2026,
            'summary' => 'A responsive dashboard interface focused on reusable UI sections, clean navigation, status cards, and fast page structure for web applications.',
            'technologies' => 'React.js, Next.js, TypeScript, Responsive UI',
            'project_url' => '',
            'repo_url' => '',
        ],
        [
            'title' => 'NestJS API Service',
            'project_type' => 'Backend API',
            'project_year' => 2026,
            'summary' => 'A TypeScript backend concept for structured API routes, request validation, service-layer logic, and database-ready application workflows.',
            'technologies' => 'NestJS, Node.js, TypeScript, REST APIs',
            'project_url' => '',
            'repo_url' => '',
        ],
        [
            'title' => 'n8n Automation Workflow',
            'project_type' => 'Automation',
            'project_year' => 2026,
            'summary' => 'A workflow automation concept for connecting APIs, handling webhook events, moving data between services, and reducing repeated manual tasks.',
            'technologies' => 'n8n, Webhooks, API Integration, Automation',
            'project_url' => '',
            'repo_url' => '',
        ],
    ]);
}

function with_pinned_projects(array $projects): array
{
    $existingKeys = [];

    foreach ($projects as $project) {
        $existingKeys[] = strtolower((string) ($project['title'] ?? ''));
        $existingKeys[] = strtolower((string) ($project['repo_url'] ?? ''));
    }

    $missing = array_filter(
        pinned_projects(),
        fn (array $project): bool => !in_array(strtolower($project['title']), $existingKeys, true)
            && !in_array(strtolower($project['repo_url']), $existingKeys, true)
    );

    return array_merge($missing, $projects);
}

function portfolio_skills(): array
{
    $pdo = portfolio_db();

    if (!$pdo) {
        return fallback_skills();
    }

    try {
        $statement = $pdo->query(
            'SELECT category, items
             FROM skills
             WHERE is_visible = 1
             ORDER BY sort_order ASC, id ASC'
        );
        $rows = $statement->fetchAll();
    } catch (Throwable $error) {
        error_log('Portfolio skills query failed: ' . $error->getMessage());
        return fallback_skills();
    }

    return $rows ? with_pinned_skills($rows) : fallback_skills();
}

function portfolio_projects(): array
{
    $pdo = portfolio_db();

    if (!$pdo) {
        return fallback_projects();
    }

    try {
        $statement = $pdo->query(
            'SELECT title, project_type, project_year, summary, technologies, project_url, repo_url
             FROM projects
             WHERE is_featured = 1
             ORDER BY sort_order ASC, id ASC'
        );
        $rows = $statement->fetchAll();
    } catch (Throwable $error) {
        error_log('Portfolio projects query failed: ' . $error->getMessage());
        return fallback_projects();
    }

    return $rows ? with_pinned_projects($rows) : fallback_projects();
}
