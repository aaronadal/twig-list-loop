# The Twig List Loop

A different way to display lists, grids and tables in [Twig](https://twig.sensiolabs.org/).

Suppose you want to create several similar tables along your web application. It would be fabulous to be able to
define one single template skeleton and reuse it in a flexible way, right? So, that is exactly what this Twig tag does.
Let's see it in action.

## Creating your first list skeleton
    
Suppose you have two tables in your application: a teachers table and a students table. You probably want to create
some list skeleton like this:

    {# table.tpl.twig #}
    <table class="table">
        <tbody>
        {% for item in list %}
            {{ item | raw }}
        {% endfor %}
        </tbody>
    </table>

You can now use the list loop like follows:

    {% list teacher in teachers using 'table.tpl.twig' %}
        <tr>
            <td>{{ teacher.id }}</td>
            <td>{{ teacher.name }}</td>
            <td>{{ teacher.subject }}</td>
        </tr>
    {% endlist %}

    {% list student in students using 'table.tpl.twig' %}
        <tr>
            <td>{{ student.id }}</td>
            <td>{{ student.name }}</td>
            <td>{{ student.teacher.name }}</td>
        </tr>
    {% endlist %}

As you can see, the syntax is practically the same than the for loop's one. The only difference is the `using`
keyword, followed by the template used as skeleton.

Like in the for loop, you also have access to the `loop` variable and you also can specify an inline `if` expression:

    {% list teacher in teachers if teacher.alive using 'table.tpl.twig' %}
        <tr>
            <td>{{ loop.index }}</td>
            <td>{{ teacher.id }}</td>
            <td>{{ teacher.name }}</td>
            <td>{{ teacher.subject }}</td>
        </tr>
    {% endlist %}

## `list` and `else` variables

When you define a new skeleton you have access to two special variables: `list` and `else`. The former is an
array containing the rendered items of the list and the later contains the rendered else statement.

Wait! The else statement? Yes, like in for loops, in list loops you can also define an else statement and display
it when the list is empty:

    {# table.tpl.twig #}
    {% if list %}
        <table class="table">
            <tbody>
            {% for item in list %}
                {{ item | raw }}
            {% endfor %}
            </tbody>
        </table>
    {% else %}
        {{- else | raw -}}
    {% endif %}

***

    {% list teacher in teachers using 'table.tpl.twig' %}
        <tr>
            <td>{{ teacher.id }}</td>
            <td>{{ teacher.name }}</td>
            <td>{{ teacher.subject }}</td>
        </tr>
    {% else %}
        <p class="error">Oops! There are no teachers.</p>
    {% endlist %}

**NOTE:** If an else statement is defined, the `else` variable will always contain it. It is the skeleton's job
to determine whether it has to be shown or not. In the example above, it is done with a simple condition: if
the list is not empty then the table is displayed, otherwise the else message is displayed.

## Passing arguments to the skeleton

Ok, this sounds pretty good, but... what happens if I want to add headers to the table? I can't hardcode it in my
skeleton because they can vary along the different tables.

Well, I have a solution for you. In this case you can pass the table headers as an argument for the skeleton.
Look at this snippet:

    {# table.tpl.twig #}
    {% if list %}
        {% if headers | default(false) %}
            <thead>
                <tr>
                    {% for header in headers %}
                        <th>{{- header -}}</th>
                    {% endfor %}
                </tr>
            </thead>
        {% endif %}
        <table class="table">
            <tbody>
            {% for item in list %}
                {{ item | raw }}
            {% endfor %}
            </tbody>
        </table>
    {% else %}
        {{- else | raw -}}
    {% endif %}

***

    {% set args = {
        headers: ['ID', 'Name', 'Subject']
    } %}
    {% list teacher in teachers using 'table.tpl.twig' with args %}
        <tr>
            <td>{{ teacher.id }}</td>
            <td>{{ teacher.name }}</td>
            <td>{{ teacher.subject }}</td>
        </tr>
    {% else %}
        <p class="error">Oops! There are no teachers.</p>
    {% endlist %}

Pretty simple, right?
