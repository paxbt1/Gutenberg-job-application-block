
wp.blocks.registerBlockType('jobapplicationablock/job-application-block', {
    title: "Job Application Block",
    icon:"smiley",
    category: "design",
    edit: function (props) {
        return React.createElement("div", null, React.createElement("p", null, "Job applications table block"));
    },
    save: function (props) {
        const options = job_application_block_vars.options;
        const optionElements = Object.keys(options).map((key) => {
            return React.createElement("option", { value: options[key] }, key);
        });
        return (
        React.createElement("div", { class: "form-container" },
        React.createElement("form", { id: "easyApply" },
        React.createElement("select", { id: "jobTitle", name: "jobTitle", placeholder: "Job Title" },
            React.createElement("option", { value: "Select Job Titl" }, "Select Job Titl"),optionElements),
        React.createElement("input", { type: "text", id: "firstName", name: "firstName", placeholder: "First Name" }),
        React.createElement("input", { type: "text", id: "lastName", name: "lastName", placeholder: "Last Name" }),
        React.createElement("input", { type: "date", id: "entryDate", name: "entryDate", placeholder: "Entry Date" }),
        React.createElement("button", { type: "submit", id: "submit-btn" }, "Submit"),
        React.createElement("input", { type: "hidden", name: "nonce_controller", id: "nonce-controller", value: job_application_block_vars.nonce })
        
            )
        )        );
     }
    
})

