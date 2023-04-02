
wp.blocks.registerBlockType('jobapplicationablock/job-application-block', {
    title: "Job Application Block",
    icon:"smiley",
    category: "design",
    edit: function (props) {
        return React.createElement("div", null, React.createElement("p", null, "Job applications table block"));
    },
    save: function (props) { 
        return (
        React.createElement("div", { class: "form-container" },
        React.createElement("form", { id: "easyApply" },
        React.createElement("select", { id: "jobTitle", name: "jobTitle", placeholder: "Job Title" },
            React.createElement("option", { value: "Select Job Titl" }, "Select Job Titl"),
            React.createElement("option", { value: "Web Developer" }, "Web Developer"),
            React.createElement("option", { value: "Software Engineer" }, "Software Engineer"),
            React.createElement("option", { value: "Data Analyst" }, "Data Analyst"),
            React.createElement("option", { value: "Project Manager" }, "Project Manager")),
        React.createElement("input", { type: "text", id: "firstName", name: "firstName", placeholder: "First Name" }),
        React.createElement("input", { type: "text", id: "lastName", name: "lastName", placeholder: "Last Name" }),
        React.createElement("input", { type: "date", id: "entryDate", name: "entryDate", placeholder: "Entry Date" }),
        React.createElement("button", { type: "submit", id: "submit-btn" }, "Submit"),
        React.createElement("input", { type: "hidden", name: "nonce_controller", id: "nonce-controller", value: job_application_block_vars.nonce })
        
            )
        )        );
     }
    
})

