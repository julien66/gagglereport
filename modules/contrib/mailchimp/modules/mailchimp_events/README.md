#Mailchimp Events
Events are used for [Behavioral Targeting](https://mailchimp.com/help/use-events-behavioral-targeting/) in Mailchimp. 

This module can turn actions taken on your Drupal site (such as filling out
a webform) into an Event in Mailchimp, directly in an Audience Member's
Activity Feed. Mailchimp then provides the tools to automate campaigns
from that event.

In Drupal, this module allows you to create Event Entities,
which represent events you want to send to Mailchimp.

An Event Entity's optional properties can store data that
you want Mailchimp to see as part of the event.

The Event Entity represents an event you want to capture,
but the capture itself won't happen until you trigger it.

The simplest way to trigger an event is with a webform handler,
but you could also create a custom implementation in code.

This module generates Events through the Mailchimp API's [Event Endpoint](https://mailchimp.com/developer/marketing/guides/track-outside-activity-events/).

## Creating an Event Type
From `Configuration > Web Services > Mailchimp > Events`
you can add a new event type.

[Mailchimp suggests](https://mailchimp.com/developer/marketing/guides/track-outside-activity-events/#create-an-event) as verb_noun pair like `registered_referral`.

Properties are completely optional and useful to capture a piece of data during
the event which Mailchimp receives when the event triggers,
usually to give the event context.

## Create an actual event
If you have enabled the [Webform module](https://www.drupal.org/project/webform) then you can add a handler to any webform `Structure > Webforms > Forms`, in the  `Settings > Emails/Handlers` then `Add handler`.

Tokens are a useful way to capture data from the form to an event property,
for Mailchimp to automate from.

## The Example submodule
The Mailchimp Events Example module is included in this project,
which adds two additional options in the Mailchimp Events Configuration area:
1) A debug Mailchimp Events tool, which allows you to grab the most recent
events from a specific audience member.
2) An `Add Event to Member` which can be used to manually generate an event,
which then gets pushed to Mailchimp. This requires an Event Entity.

The code for this module can also be a useful reference to trigger
an event in code.

## What this module provides for developers

### Adds two helper functions
Creates two helper functions around the Mailchimp API methods to add and
retrieve Member Events.

The two functions are:

```
mailchimp_events_list_member_events
```

which gets a set of events for a given member in a given audience given an
audience id and an email address. Additional parameters documented in the
MAILCHIMP [MARKETING API](https://mailchimp.com/developer/marketing/api/list-member-events/list-member-events/)
are also available as optional parameters (count, offset, fields,
exclude_fields).

```
mailchimp_events_add_member_event
```

which adds an event to a member in an audience given a audience id, an email
address, and the name of the event. Additional parameters documented in the
[MAILCHIMP MARKETING API](https://mailchimp.com/developer/marketing/api/list-member-events/add-event/)
are also available as optional parameters (properties, is_syncing,
occurred_at).

## Examples module for debugging
You can try out these helper functions in an isolated way by enabling the
mailchimp_events_example module and visiting the two debugging pages:

`/admin/config/services/mailchimp/event/debug/add`

to test out adding an event to an email address and

`/admin/config/services/mailchimp/event/debug`

to retrieve the list of events for the email address.
