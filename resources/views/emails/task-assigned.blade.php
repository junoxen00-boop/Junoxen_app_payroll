<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Task Assigned</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f9; font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 15px;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                       style="background:#ffffff; border-radius:10px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#4f46e5; padding:25px; text-align:center;">

                            <h1 style="margin:0; color:#ffffff; font-size:24px;">
                                Junoxen Employee Management
                            </h1>

                            <p style="margin:8px 0 0; color:#e0e7ff; font-size:14px;">
                                New Task Assigned
                            </p>

                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:35px;">

                            <h2 style="margin-top:0; color:#1f2937;">
                                Hello {{ $employee->full_name }},
                            </h2>

                            <p style="font-size:16px; color:#4b5563; line-height:1.6;">
                                A new task has been assigned to you.
                            </p>

                            <!-- Task Details -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                   style="margin:25px 0; border:1px solid #e5e7eb; border-radius:8px;">

                                <tr>
                                    <td colspan="2"
                                        style="background:#4f46e5; color:#ffffff; padding:14px; font-size:17px; font-weight:bold;">
                                        Task Details
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px; font-weight:bold; width:35%; border-bottom:1px solid #e5e7eb;">
                                        Task
                                    </td>
                                    <td style="padding:12px; border-bottom:1px solid #e5e7eb;">
                                        {{ $task->title }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px; font-weight:bold; border-bottom:1px solid #e5e7eb;">
                                        Priority
                                    </td>
                                    <td style="padding:12px; border-bottom:1px solid #e5e7eb;">
                                        {{ $task->priority }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px; font-weight:bold; border-bottom:1px solid #e5e7eb;">
                                        Start Date
                                    </td>
                                    <td style="padding:12px; border-bottom:1px solid #e5e7eb;">
                                        {{ $task->start_date ?? '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px; font-weight:bold;">
                                        Due Date
                                    </td>
                                    <td style="padding:12px;">
                                        {{ $task->due_date ?? '-' }}
                                    </td>
                                </tr>

                            </table>

                            @if($task->description)
                                <p style="font-size:15px; color:#4b5563; line-height:1.6;">
                                    <strong>Description:</strong><br>
                                    {{ $task->description }}
                                </p>
                            @endif

                            <p style="font-size:16px; color:#4b5563; line-height:1.6;">
                                Please log in to the Employee Management System
                                to view and complete your assigned task.
                            </p>

                            
                           

                            <p style="font-size:15px; color:#4b5563; line-height:1.6;">
                                Regards,<br>
                                <strong>Junoxen Employee Management</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f9fafb; padding:20px; text-align:center;">

                            <p style="margin:0; font-size:12px; color:#9ca3af;">
                                This is an automated notification from Junoxen Employee Management.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>