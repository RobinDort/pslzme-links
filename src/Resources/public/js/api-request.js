function handleAPIRequest(requestObject) {
	try {
		return new Promise(function (resolve, reject) {
			// Send the second AJAX request
			$.ajax({
				url: "/requestHandler",
				type: "POST",
				data: requestObject,
				success: function (response) {
					console.log("Action called: " + requestObject.request);
					console.log(response);
					resolve(response);
				},
				error: function (xhr, status, error) {
					console.log("Action failed: " + requestObject.request);
					console.log("Error Status:", status); // E.g. "error", "timeout", "abort", etc.
					console.log("XHR Response:", xhr.responseText); // The response from the server
					console.log("Error Thrown:", error); // Detailed error message
				},
			});
		});
	} catch (error) {
		console.error(error);
	}
}
