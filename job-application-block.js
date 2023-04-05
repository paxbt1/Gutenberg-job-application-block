wp.blocks.registerBlockType('jobapplicationablock/job-application-block', {
    title: "Job Application Block",
    icon: "smiley",
    category: "design",
    edit: function(props) {
        const jobTitles = wp.data.select('core').getEntityRecords('postType', 'job_title');
        const optionElements = jobTitles ? jobTitles.map((jobTitle) => {
            return React.createElement("option", { value: jobTitle.id }, jobTitle.title.rendered);
        }) : null;


        return (
            React.createElement("div", { class: "form-container" },
                React.createElement("form", { id: "easyApply" },
                    React.createElement("select", { id: "jobTitle", name: "jobTitle", placeholder: "Job Title" },
                        React.createElement("option", { value: "" }, "Select Job Title"), optionElements),
                    React.createElement("input", { type: "text", id: "firstName", name: "firstName", placeholder: "First Name" }),
                    React.createElement("input", { type: "text", id: "lastName", name: "lastName", placeholder: "Last Name" }),
                    React.createElement("input", { type: "date", id: "entryDate", name: "entryDate", placeholder: "Entry Date" }),
                    React.createElement("button", { type: "submit", id: "submit-btn" }, "Submit"),
                    React.createElement("input", { type: "hidden", name: "nonce_controller", id: "nonce-controller", value: job_application_block_vars.nonce })

                )
            ));
    },

    save: function(props) {
        const jobTitles = wp.data.select('core').getEntityRecords('postType', 'job_title');
        const optionElements = jobTitles ? jobTitles.map((jobTitle) => {
            return React.createElement("option", { value: jobTitle.id }, jobTitle.title.rendered);
        }) : null;


        return (
            React.createElement("div", { class: "form-container" },
                React.createElement("form", { id: "easyApply" },
                    React.createElement("select", { id: "jobTitle", name: "jobTitle", placeholder: "Job Title" },
                        React.createElement("option", { value: "" }, "Select Job Title"), optionElements),
                    React.createElement("input", { type: "text", id: "firstName", name: "firstName", placeholder: "First Name" }),
                    React.createElement("input", { type: "text", id: "lastName", name: "lastName", placeholder: "Last Name" }),
                    React.createElement("input", { type: "date", id: "entryDate", name: "entryDate", placeholder: "Entry Date" }),
                    React.createElement("button", { type: "submit", id: "submit-btn" }, "Submit"),
                    React.createElement("input", { type: "hidden", name: "nonce_controller", id: "nonce-controller", value: job_application_block_vars.nonce })

                )
            ));
    }

})