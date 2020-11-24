class Rest {

    static get(data) { 
        return $.get("rest/", data)
    }

    static post(data) { 
        return $.post("rest/", data)
    }

    static put(data) { 
        return $.ajax({
            url: 'rest/',
            type: 'PUT',
            data: JSON.stringify(data)
        })
    }

    static delete(data) { 
        return $.ajax({
            url: 'rest/',
            type: 'DELETE',
            data: JSON.stringify(data)
        })
    }

}