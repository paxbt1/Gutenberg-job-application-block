Plugin Name: Job Application Block
Description
The Job Application Block plugin allows you to easily display a table with job applications submitted by users. It also includes a form for submitting job applications and a custom post type for job titles with a custom field of type multiple-select to enter skills related to each job title also a custom post type called Job apllication for storing the job applications .

Features
Displays a table with five columns (job title, first name, last name, entry date, skills required)
Allows users to submit job applications using a form with four fields (job title, first name, last name, entry date)
Includes a custom post type "Job titles" with a custom field of type multiple-select named "Skills" to enter skills related to each job title
Includes a custom post type "Job Applications" for storing users job applications.
Implements a job title filter at the top of the table to filter the table entries based on the job title name.
Implements a skills filter at the top of the table to filter the table entries based on the selected skill.
Implements pagination which can set length from block editor.
Forbidden user to submit form with empty field.
Forbidden admin to use block without defining Job Title and of course Skills. also implementing reference link to create job title skills.
Fully responsive
Installation
Upload the plugin files to the /wp-content/plugins/job-application-table directory, or install the plugin through the WordPress plugins screen directly.
Activate the plugin through the 'Plugins' screen in WordPress.
Add terms to the Skills taxonomy by going to the Job Titles post type and selecting the Skills taxonomy from the left sidebar. 
Define job titles by creating new Job Title posts and selecting related skills from the multi-select input box.
Add the Job Application block to your Page or Post throw Gutenberg editor/ blocks list/ design section.
Usage
After installing the plugin, it will add two custom post types: "Job titles" and "Job applications". In order to use the Job Application block, you need to add terms to the Skills taxonomy and define job titles by creating new Job Title posts and selecting related skills from the multi-select input box.

To add the Job Application block in your page, go to the Gutenberg editor/ blocks list/ design section and select the Job Application block. You can then use the form to submit job applications, which will be added to the Job Applications custom post type and displayed in the Job Application Block Table.

Security
The plugin has been developed with security in mind and all inputs are sanitized to prevent SQL injections and other security vulnerabilities.

Compatibility
The plugin has been tested with the latest version of WordPress and is compatible with most themes and theme builders. However, if you encounter any compatibility issues, please let us know.

Support
If you need help using the plugin or have any questions or feedback, please contact us at paxbit1@gmail.com.

Credits
The Job Application Block plugin was developed by Saeed Ghourbanian Rafaat as a Test Task.
