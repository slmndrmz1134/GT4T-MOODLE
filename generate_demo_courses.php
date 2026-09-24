<?php
// GT4T Moodle - CLI course generator tool.
// Programmatically generates 10 realistic demo courses with completed content
// and distributes them round-robin among the 3 university partner categories.

define('CLI_SCRIPT', true);
require(__DIR__ . '/config.php');
require_once($CFG->libdir. '/clilib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/admin/tool/generator/classes/backend.php');
require_once($CFG->dirroot . '/admin/tool/generator/classes/course_backend.php');

// Force admin user context so generator runs with full permissions.
\core\session\manager::set_user(get_admin());

// List of realistic demo courses to generate
$courses_to_create = [
    // 1. Algebra University College (Category ID: 3)
    [
        'fullname' => 'Applied Machine Learning',
        'shortname' => 'ALG-AML-101',
        'summary' => 'Comprehensive introduction to machine learning techniques, models, and practical implementations using Python.',
        'categoryid' => 3
    ],
    [
        'fullname' => 'Advanced Software Engineering Patterns',
        'shortname' => 'ALG-ASE-201',
        'summary' => 'Deep dive into microservices, enterprise integration patterns, cloud-native architectures, and software design principles.',
        'categoryid' => 3
    ],
    [
        'fullname' => 'Cybersecurity Foundations & Threat Hedging',
        'shortname' => 'ALG-CSF-301',
        'summary' => 'Core concepts in network security, threat modeling, encryption, and proactive defense mechanisms.',
        'categoryid' => 3
    ],
    [
        'fullname' => 'Data Science & Predictive Analytics',
        'shortname' => 'ALG-DSP-401',
        'summary' => 'Analysis of structured and unstructured datasets, prediction models, and data visualization.',
        'categoryid' => 3
    ],

    // 2. Istanbul Beykent University (Category ID: 4)
    [
        'fullname' => 'Digital Marketing & Growth Hacking',
        'shortname' => 'BEY-DMG-101',
        'summary' => 'Modern marketing channels, SEO, search and social advertising, and metric-driven user acquisition.',
        'categoryid' => 4
    ],
    [
        'fullname' => 'Strategic Project Management',
        'shortname' => 'BEY-SPM-201',
        'summary' => 'Project lifecycles, Agile and Scrum methodologies, resource allocation, and team optimization.',
        'categoryid' => 4
    ],
    [
        'fullname' => 'Microeconomics and Global Trade Policy',
        'shortname' => 'BEY-MGT-301',
        'summary' => 'Supply-demand mechanics, market equilibrium, tariff structures, and international economic regulations.',
        'categoryid' => 4
    ],

    // 3. Technische Hochschule Rosenheim (Category ID: 7)
    [
        'fullname' => 'Sustainable Wood Technology & Construction',
        'shortname' => 'ROS-SWT-101',
        'summary' => 'Engineering properties of timber, sustainable harvesting, and modern mass-timber construction styles.',
        'categoryid' => 7
    ],
    [
        'fullname' => 'Automation Systems & Robotics',
        'shortname' => 'ROS-ASR-201',
        'summary' => 'Programmable logic controllers (PLCs), robotic kinematics, and feedback control loops.',
        'categoryid' => 7
    ],
    [
        'fullname' => 'Renewable Energy Grid Integration',
        'shortname' => 'ROS-REG-301',
        'summary' => 'Photovoltaics, wind turbine mechanics, smart grid distribution, and battery storage engineering.',
        'categoryid' => 7
    ]
];

echo "===============================================\n";
echo "GT4T - STARTING DEMO COURSE GENERATOR CLI SCRIPT\n";
echo "===============================================\n\n";

foreach ($courses_to_create as $index => $c) {
    $num = $index + 1;
    echo "[Course {$num}/10] Checking: {$c['fullname']} ({$c['shortname']})...\n";

    // Skip if course already exists
    if ($DB->record_exists('course', ['shortname' => $c['shortname']])) {
        echo "--> Course already exists. Skipping.\n\n";
        continue;
    }

    echo "--> Generating course data (Size: XS)...\n";
    
    // Size '0' corresponds to 'XS' (Extra Small) in tool_generator
    $backend = new tool_generator_course_backend(
        $c['shortname'],
        0,              // Size index 0 (XS)
        true,           // Fixed dataset for predictable structures
        false,          // No filesize limit
        true,           // Output generator dots progress
        $c['fullname'],
        $c['summary']
    );

    // Run core generator to create course, assignment, page, and forum
    $courseid = $backend->make();

    echo "--> Moving Course ID {$courseid} to Partner Category ID {$c['categoryid']}...\n";
    try {
        move_courses([$courseid], $c['categoryid']);
        $category = core_course_category::get($c['categoryid']);
        echo "--> Successfully generated and moved to '{$category->name}'!\n\n";
    } catch (Exception $e) {
        echo "--> ERROR moving course to category {$c['categoryid']}: " . $e->getMessage() . "\n\n";
    }
}

echo "===============================================\n";
echo "Purging all Moodle caches to display new courses...\n";
purge_all_caches();
echo "Purging caches completed successfully.\n";
echo "===============================================\n";
echo "DEMO COURSE GENERATION SCRIPT COMPLETED!\n";
echo "===============================================\n";
