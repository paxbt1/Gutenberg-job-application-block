wp.blocks.registerBlockType('jobapplicationablock/job-application-block', {
    title: "Job Application Block",
    icon: "smiley",
    category: "design",
    attributes: {
        paginationLength: {type:'string'}  
    },
    edit: function (props) {

        function savePagination(event) {
            props.setAttributes({ paginationLength : event.target.value})
        }
        if (!job_application_block_vars.init_detect) {
            return (
                React.createElement("div", null,
                    React.createElement("p", null, "Cannot create block. You need to define the job title and skills first."),
                    React.createElement("a", {href:job_application_block_vars.admin_url},'click here')
                )
            );

        } else {

            return (
                React.createElement("div", null,
                    React.createElement("p",null,"Job Application Block"),
                    React.createElement("label", null, "Pagination Length:"),
                    React.createElement("input", {
                        type: "number",
                        value: props.attributes.paginationLength,
                        placeholder:"Insert number",
                        onChange: savePagination
                    })

                )
            );
        }
    },

    save: function (props) {
        
        if (!job_application_block_vars.init_detect) {
            return null;
        } else {
            return (
                React.createElement("div", { class: "form-container" },

                    React.createElement("form", { id: "easyApply" },
                        React.createElement("div", { class: "inputs-container" },
                            React.createElement("select", { id: "jobTitle", name: "jobTitle", placeholder: "Job Title" },
                                React.createElement("option", { value: "" }, "Select Job Title")),
                            React.createElement("input", { type: "text", id: "firstName", name: "firstName", placeholder: "First Name" }),
                            React.createElement("input", { type: "text", id: "lastName", name: "lastName", placeholder: "Last Name" }),
                            React.createElement("input", { type: "date", id: "entryDate", name: "entryDate", placeholder: "Entry Date" }),
                        ),
                        React.createElement("button", { type: "submit", id: "submit-btn" }, "Submit"),
                    ),
                    React.createElement("input",{type:"hidden",id:"paginationLength",value:props.attributes.paginationLength}),
                    React.createElement("div", null,
                        React.createElement("table", {
                            class: "job-applications-table"
                        },
                            React.createElement("div", { class: "filter-container" },
                                React.createElement("div", null,
                                React.createElement('label', { id: "filter-label", for: 'jobTitleFilter' }, "Filter by Job Title:"),
                                React.createElement("select", { id: "jobTitleFilterSelect", name: "jobTitleFilter", placeholder: "Job Title" },
                                    React.createElement("option", { value: "-1" }, "Show All"))
                                ),
                                React.createElement("div", null,
                                React.createElement('label', { id: "filter-label", for: 'skillFilter' }, "Filter by Skill:"),
                                React.createElement("select", { id: "skillFilterSelect", name: "skillTitleFilter", placeholder: "Skill" },
                                    React.createElement("option", { value: "-1" }, "Show All"))
                                ),
                            ),
                            React.createElement("thead", null,
                                React.createElement("tr", null,
                                    React.createElement("th", null, "Job Title"),
                                    React.createElement("th", null, "First Name"),
                                    React.createElement("th", null, "Last Name"),
                                    React.createElement("th", null, "Entry Date"),
                                    React.createElement("th", null, "Job Skills"))),
                            React.createElement("tbody", null))
                    ),
                    React.createElement("div", { class: "pagination-container" })


                )
            );
        }
    }
})