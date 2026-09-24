<?php
// GT4T Moodle - CLI Course Content Expander & Cover Image Mapper.
// Boots Moodle, clears existing minimal modules for the 10 demo courses,
// builds 4 beautiful topic sections with detailed learning materials,
// and maps the generated premium cover images to each course overview card.

define('CLI_SCRIPT', true);
require(__DIR__ . '/config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/lib/testing/classes/util.php');
require_once($CFG->dirroot . '/lib/testing/generator/lib.php');

// Force admin user context
\core\session\manager::set_user(get_admin());

// Check if generator classes are loaded
require_once($CFG->dirroot . '/lib/testing/generator/lib.php');

$courses_shortnames = [
    'ALG-AML-101', 'ALG-ASE-201', 'ALG-CSF-301', 'ALG-DSP-401',
    'BEY-DMG-101', 'BEY-SPM-201', 'BEY-MGT-301',
    'ROS-SWT-101', 'ROS-ASR-201', 'ROS-REG-301'
];

// Mapping of shortname prefix to cover image filenames
$image_mapping = [
    'ALG' => 'course_images/ALG-AML-101.png',
    'BEY' => 'course_images/BEY-DMG-101.png',
    'ROS' => 'course_images/ROS-SWT-101.png'
];

echo "===============================================\n";
echo "GT4T - EXPANDING DEMO COURSES & MAPPING IMAGES\n";
echo "===============================================\n\n";

$generator = testing_util::get_data_generator();
$fs = get_file_storage();

foreach ($courses_shortnames as $shortname) {
    echo "Processing course: {$shortname}...\n";

    // 1. Fetch course record
    $course = $DB->get_record('course', ['shortname' => $shortname]);
    if (!$course) {
        echo "--> Course not found! Skipping.\n\n";
        continue;
    }

    $coursecontext = context_course::instance($course->id);

    // 2. Delete existing modules to build fresh rich content
    echo "--> Clearing old modules...\n";
    $cms = get_fast_modinfo($course->id)->get_cms();
    foreach ($cms as $cm) {
        course_delete_module($cm->id);
    }

    // 3. Ensure 4 topic sections exist
    echo "--> Creating topic sections...\n";
    course_create_sections_if_missing($course, [0, 1, 2, 3, 4]);

    // 4. Generate expanded course materials
    echo "--> Generating rich learning resources & activities...\n";
    
    // SECTION 0 - Announcements & General Info
    $forumgenerator = $generator->get_plugin_generator('mod_forum');
    $forumgenerator->create_instance([
        'course' => $course->id,
        'section' => 0,
        'name' => 'General Announcements & Class Q&A',
        'intro' => '<p>Welcome to the course! Use this forum to stay updated on key deadlines and discuss course material with your peers.</p>',
        'introformat' => FORMAT_HTML
    ]);

    // SECTION 1 - Syllabus & Study Pathway
    $pagegenerator = $generator->get_plugin_generator('mod_page');
    $pagegenerator->create_instance([
        'course' => $course->id,
        'section' => 1,
        'name' => 'Course Syllabus & Study Guidelines',
        'intro' => '<p>The complete syllabus, learning outcomes, and assessment breakdown.</p>',
        'introformat' => FORMAT_HTML,
        'content' => '
            <h3>1. Course Overview</h3>
            <p>Welcome to <strong>' . s($course->fullname) . '</strong>. This program is designed to give you comprehensive theoretical insights alongside rigorous practical exercises.</p>
            <h3>2. Intended Learning Outcomes</h3>
            <ul>
                <li>Analyze and implement complex foundational systems.</li>
                <li>Design cloud-native architecture modules aligned with global standards.</li>
                <li>Critically evaluate system performance under high workloads.</li>
            </ul>
            <h3>3. Assessment & Grading Rubric</h3>
            <table border="1" cellpadding="8" style="border-collapse:collapse; width:100%; border-color:#e2e8f0;">
                <thead>
                    <tr style="background:#f1f5f9;">
                        <th>Deliverable</th>
                        <th>Weight</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Weekly Practical Exercises</td>
                        <td>30%</td>
                        <td>End of Week 2</td>
                    </tr>
                    <tr>
                        <td>Mid-term Case Study Analysis</td>
                        <td>30%</td>
                        <td>End of Week 3</td>
                    </tr>
                    <tr>
                        <td>Final Capstone Project</td>
                        <td>40%</td>
                        <td>End of Week 4</td>
                    </tr>
                </tbody>
            </table>
        ',
        'contentformat' => FORMAT_HTML
    ]);

    // SECTION 2 - Foundations & Core Application
    $pagegenerator->create_instance([
        'course' => $course->id,
        'section' => 2,
        'name' => 'Week 1: Core Theoretical Foundations',
        'intro' => '<p>Essential reading and module content for Week 1.</p>',
        'introformat' => FORMAT_HTML,
        'content' => '
            <h3>Week 1 - Deep Dive</h3>
            <p>In this module, we introduce the core paradigms and primary mechanisms that define this field. Review the architectural blueprints below to establish your foundational workflow.</p>
            <blockquote style="border-left:4px solid #1DC71D; padding-left:12px; margin-left:0; color:#475569;">
                "To build resilient systems, one must master the underlying mathematical and logic matrices before scaling modular elements."
            </blockquote>
            <h3>Key Study Areas:</h3>
            <ol>
                <li>Input parameters validation and normalization.</li>
                <li>Dynamic thread allocation and background tasks handlers.</li>
                <li>State management models and persistence layers.</li>
            </ol>
        ',
        'contentformat' => FORMAT_HTML
    ]);

    $assigngenerator = $generator->get_plugin_generator('mod_assign');
    $assigngenerator->create_instance([
        'course' => $course->id,
        'section' => 2,
        'name' => 'Assignment 1: Weekly Practical Case Study',
        'intro' => '<p>Submit your answers to the theoretical questions and upload your system blueprint diagrams here.</p>
                    <p><strong>Submission Format:</strong> PDF (maximum 10MB).</p>',
        'introformat' => FORMAT_HTML,
        'nosubmissions' => 0,
        'submissiondrafts' => 0
    ]);

    // SECTION 3 - Intermediate Scaling
    $pagegenerator->create_instance([
        'course' => $course->id,
        'section' => 3,
        'name' => 'Week 2: Advanced Implementations',
        'intro' => '<p>Advanced learning assets and design patterns for Week 2.</p>',
        'introformat' => FORMAT_HTML,
        'content' => '
            <h3>Week 2 - Optimization</h3>
            <p>Building on foundations, we explore strategies to optimize latency, refactor redundant components, and execute scalability tests.</p>
            <h3>Expected Deliverables:</h3>
            <ul>
                <li>Benchmark analytics diagrams.</li>
                <li>Modular workflow designs showing clear division of system responsibilities.</li>
            </ul>
        ',
        'contentformat' => FORMAT_HTML
    ]);

    // SECTION 4 - Capstone & Final Submissions
    $pagegenerator->create_instance([
        'course' => $course->id,
        'section' => 4,
        'name' => 'Final Capstone Project Guidelines',
        'intro' => '<p>Comprehensive instructions for your final course capstone project.</p>',
        'introformat' => FORMAT_HTML,
        'content' => '
            <h3>Capstone Project Outline</h3>
            <p>Your capstone project is the final synthesis of your work during this course. You must design and deliver a fully operational system proposal.</p>
            <p><strong>Requirements:</strong></p>
            <ul>
                <li>Must feature structured error diagnostics.</li>
                <li>Must address real-world deployment limitations.</li>
                <li>Must include a detailed architectural walkthrough.</li>
            </ul>
        ',
        'contentformat' => FORMAT_HTML
    ]);

    $assigngenerator->create_instance([
        'course' => $course->id,
        'section' => 4,
        'name' => 'Final Capstone Project Submission Portal',
        'intro' => '<p>Please upload your final Capstone Project package here before the deadline.</p>
                    <p><strong>Required:</strong> ZIP archive containing the report, project schematics, and video walkthrough link.</p>',
        'introformat' => FORMAT_HTML,
        'nosubmissions' => 0,
        'submissiondrafts' => 0
    ]);

    // 5. Map Cover Image to Course Summary (overviewfiles)
    $prefix = substr($shortname, 0, 3);
    if (isset($image_mapping[$prefix])) {
        $localimage = $image_mapping[$prefix];
        echo "--> Mapping cover image '{$localimage}'...\n";
        
        // Delete any existing overview files first to avoid duplicates
        $fs->delete_area_files($coursecontext->id, 'course', 'overviewfiles', 0);
        
        $filerecord = [
            'contextid' => $coursecontext->id,
            'component' => 'course',
            'filearea' => 'overviewfiles',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'course_cover.png',
            'userid' => get_admin()->id
        ];
        
        try {
            $fs->create_file_from_pathname($filerecord, __DIR__ . '/' . $localimage);
            echo "--> Successfully mapped cover image!\n";
        } catch (Exception $e) {
            echo "--> ERROR mapping image: " . $e->getMessage() . "\n";
        }
    }

    echo "Completed course {$shortname}!\n\n";
}

echo "===============================================\n";
echo "Purging all Moodle caches to compile and display rich content...\n";
purge_all_caches();
echo "Purging caches completed successfully.\n";
echo "===============================================\n";
echo "DEMO COURSE EXPANSION COMPLETED SUCCESSFULLY!\n";
echo "===============================================\n";
