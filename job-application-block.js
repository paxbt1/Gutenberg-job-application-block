wp.blocks.registerBlockType('jobapplicationablock/job-application-block', {
    title: "Job Application Block",
    icon: "smiley",
    category: "design",
    edit: function(props) {
        const jobTitles = wp.data.select('core').getEntityRecords('postType', 'job_title');
        const optionElements = jobTitles ? jobTitles.map((jobTitle) => {
            return React.createElement("option", { value: jobTitle.id }, jobTitle.title.rendered);
        }) : null;


        return
        // TODO
        // Private mode setting could be add here
        null
            
            ;
    },

    save: function(props) {
        
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
                    
                    React.createElement("div", null, 
                    React.createElement("table", {
                    class: "job-applications-table"
                    }, 
                    React.createElement("thead", null, 
                    React.createElement("tr", null, 
                    React.createElement("th", null, "Job Title"), 
                    React.createElement("th", null, "First Name"), 
                    React.createElement("th", null, "Last Name"), 
                    React.createElement("th", null, "Entry Date"), 
                    React.createElement("th", null, "Job Skills"))), 
                    React.createElement("tbody", null)))

                                    )
            ));
    }

})