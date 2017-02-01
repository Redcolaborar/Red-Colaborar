H5P.externalDispatcher.on('xAPI', function(event) { 
// Statement is available at: event.data.statement 

var statement =  event.data.statement;
//
if(typeof statement.actor != "object" || typeof statement.actor.actor != "undefined" || typeof statement.object != "object"  || typeof statement.verb != "object" || typeof statement.verb.id != "string" ||  statement.verb.id == "http://adlnet.gov/expapi/verbs/interacted")
	return;
//Check if statement actor, verb and object is present or not. 

// Send the statement using xAPI Wrapper code you added earlier. 
ADL.XAPIWrapper.sendStatement(statement);

});
