<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Shared Access Notification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style type="text/css">
      /**
   * Google webfonts. Recommended to include the .woff version for cross-client compatibility.
   */
      @media screen {
        @font-face {
          font-family: "Source Sans Pro";
          font-style: normal;
          font-weight: 400;
          src: local("Source Sans Pro Regular"), local("SourceSansPro-Regular"),
            url(https://fonts.gstatic.com/s/sourcesanspro/v10/ODelI1aHBYDBqgeIAH2zlBM0YzuT7MdOe03otPbuUS0.woff)
              format("woff");
        }
        @font-face {
          font-family: "Source Sans Pro";
          font-style: normal;
          font-weight: 700;
          src: local("Source Sans Pro Bold"), local("SourceSansPro-Bold"),
            url(https://fonts.gstatic.com/s/sourcesanspro/v10/toadOcfmlt9b38dHJxOBGFkQc6VGVFSmCnC_l7QZG60.woff)
              format("woff");
        }
      }
      /**
   * Avoid browser level font resizing.
   * 1. Windows Mobile
   * 2. iOS / OSX
   */
      body,
      table,
      td,
      a {
        -ms-text-size-adjust: 100%; /* 1 */
        -webkit-text-size-adjust: 100%; /* 2 */
      }
      /**
   * Remove extra space added to tables and cells in Outlook.
   */
      table,
      td {
        mso-table-rspace: 0pt;
        mso-table-lspace: 0pt;
      }
      /**
   * Better fluid images in Internet Explorer.
   */
      img {
        -ms-interpolation-mode: bicubic;
      }
      /**
   * Remove blue links for iOS devices.
   */
      a[x-apple-data-detectors] {
        font-family: inherit !important;
        font-size: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
        color: inherit !important;
        text-decoration: none !important;
      }
      /**
   * Fix centering issues in Android 4.4.
   */
      div[style*="margin: 16px 0;"] {
        margin: 0 !important;
      }
      body {
        width: 100% !important;
        height: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      /**
   * Collapse table borders to avoid space between cells.
   */
      table {
        border-collapse: collapse !important;
      }
      a {
        color: #1a82e2;
      }
      img {
        height: auto;
        line-height: 100%;
        text-decoration: none;
        border: 0;
        outline: none;
      }
    </style>
  </head>
  <body style="background-color: #e9ecef">
    <!-- start preheader -->
    <div
      class="preheader"
      style="
        display: none;
        max-width: 0;
        max-height: 0;
        overflow: hidden;
        font-size: 1px;
        line-height: 1px;
        color: #fff;
        opacity: 0;
      "
    >
        @php
          $requestRole = $details['requestRole'];
          $loginUser = $details['loginUser'];
          $role = $details['role'];
          if($details['role'] == "office"){
            echo "We wanted to inform you that user requested Back Office Support access!";
          }
          elseif($details['role'] == "admin"){
            echo "We wanted to inform you that user requested Administrative Support access!";
          }
          else{
            echo "We wanted to inform you that access has been successfully shared!";
          }
        @endphp
      
    </div>
    @php
      if($details['requestRole'] == "office")
      {
        $requestRole = "Back Office Support";
      }
      if($requestRole=="admin")
      {
        $requestRole = "Administrative Support";
      }
    @endphp
    
    
    <h3>
        <!--{{$loginUser->first_name}}/{{$loginUser->last_name}}/{{$loginUser->email}} has given you {{ $role }} user access to their account on Fed Benefit-->
    </h3>
    <!-- end preheader -->

    <!-- start body -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <!-- start logo -->
      <tr>
        <td align="center" bgcolor="#e9ecef">
          <!--[if (gte mso 9)|(IE)]>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
        <td align="center" valign="top" width="600">
        <![endif]-->
          <table
            border="0"
            cellpadding="0"
            cellspacing="0"
            width="100%"
            style="max-width: 600px"
          >
            <tr>
              <td align="center" valign="top" style="padding: 36px 24px">
                <a
                  href="https://engagedlearning.net/"
                  target="_blank"
                  style="
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    text-decoration: none;
                  "
                >
                  <img
                    src="https://drive.google.com/uc?export=view&id=1eVwwMv48P24eabKZVPHOvdTQHxooh2u4"
                    alt="Logo"
                    border="0"
                    width="48"
                    style="
                      display: block;
                      width: 48px;
                      max-width: 48px;
                      min-width: 48px;
                    "
                  />
                  <span
                    style="
                      letter-spacing: 2px;
                      text-transform: uppercase;
                      color: #1f263e;
                      font-size: 26px;
                      font-size: 30px;
                      font-family: 'Nunito', sans-serif;
                    "
                    >FED BENEFIT</span
                  >
                </a>
              </td>
            </tr>
          </table>
          <!--[if (gte mso 9)|(IE)]>
        </td>
        </tr>
        </table>
        <![endif]-->
        </td>
      </tr>
      <!-- end logo -->

      <!-- start hero -->
      <tr>
        <td align="center" bgcolor="#e9ecef">
          <!--[if (gte mso 9)|(IE)]>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
        <td align="center" valign="top" width="600">
        <![endif]-->
          <table
            border="0"
            cellpadding="0"
            cellspacing="0"
            width="100%"
            style="max-width: 600px"
          >
            <tr>
              <td
                align="left"
                bgcolor="#ffffff"
                style="
                  padding: 36px 24px 0;
                  font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif;
                  border-top: 3px solid #d4dadf;
                "
              >
                <h1
                  style="
                    margin: 0;
                    font-size: 28px;
                    font-weight: 700;
                    letter-spacing: -1px;
                    line-height: 40px;
                  "
                >
                    
                    @if($role == "office")
                    {{$loginUser->first_name}} {{$loginUser->last_name}}/{{$loginUser->email}} has requested {{$requestRole}} user access of your account on Fed Benefit.
                    
                    @elseif($role == "admin")
                    {{$loginUser->first_name}} {{$loginUser->last_name}}/{{$loginUser->email}} has requested {{$requestRole}} user access of your account on Fed Benefit.
                    
                    @else
                    {{$loginUser->first_name}} {{$loginUser->last_name}}/{{$loginUser->email}} has given you {{$requestRole}} user access to their account on Fed Benefit.
                    
                    @endif
                    
                
                </h1>
              </td>
            </tr>
          </table>
          <!--[if (gte mso 9)|(IE)]>
        </td>
        </tr>
        </table>
        <![endif]-->
        </td>
      </tr>
      <!-- end hero -->

      <!-- start copy block -->
      <tr>
        <td align="center" bgcolor="#e9ecef">
          <!--[if (gte mso 9)|(IE)]>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
        <td align="center" valign="top" width="600">
        <![endif]-->
          <table
            border="0"
            cellpadding="0"
            cellspacing="0"
            width="100%"
            style="max-width: 600px"
          >
            <!-- start copy -->
            <tr>
              <td
                align="left"
                bgcolor="#ffffff"
                style="
                  padding: 24px;
                  font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif;
                  font-size: 16px;
                  line-height: 24px;
                "
              >
                <p style="margin: 0">
                    @if($role == "office")
                      With this, the user will be able to:
                    @elseif($role == "admin")
                      With this, the user will be able to:
                    @else
                      With this, you will be able to:
                    @endif
                 </p>
                 @if($role == "office")
                 
                        <ul style="list-style-type: none; font-size: 14px;">
                             <li>View all of your cases.</li>
                             <li>Edit Cases.</li>
                             <li>Create new cases on your behalf.</li>
                             <li>View and print reports.</li>
                        </ul>
                 
                 @elseif($role == "admin")
                 
                        <ul style="list-style-type: none; font-size: 14px;">
                             <li>View all of your cases.</li>
                             <li>View and print reports.</li>
                        </ul>
                 
                 @else
                 
                         @if($requestRole == "Back Office Support")
                         
                         <ul style="list-style-type: none; font-size: 14px;">
                             <li>View all of their cases.</li>
                             <li>Edit Cases.</li>
                             <li>Create new cases on their behalf.</li>
                             <li>View and print reports.</li>
                        </ul>
                        
                        @endif
                        
                        @if($requestRole == "Administrative Support")
                        
                         <ul style="list-style-type: none; font-size: 14px;">
                             <li>View all of their cases.</li>
                             <li>View and print reports.</li>
                        </ul>
                        
                        @endif
                        
                @endif
                
                <p style="margin: 0">
                    @if($role == "office")
                    
                    The user will not be able to:
                    
                    @elseif($role == "admin")
                    
                    The user will not be able to:
                    
                    @else
                    
                        You will not be able to:
                        
                    @endif
                    
                </p>
                
                @if($role == "office")
                
                        <ul style="list-style-type: none; font-size: 14px;">
                             <li>Edit billing or profile information.</li>
                             <li>Invite others to your account.</li>
                             <li>View your messages</li>
                        </ul>
                
                @elseif($role == "admin")
                
                        <ul style="list-style-type: none; font-size: 14px;">
                             <li>Edit cases.</li>
                             <li>Create new cases.</li>
                             <li>Edit billing or profile information.</li>
                             <li>Invite others to your account.</li>
                             <li>View your messages.</li>
                        </ul>
                
                @else
                
                        @if($requestRole == "Back Office Support")
                        
                        <ul style="list-style-type: none; font-size: 14px;">
                             <li>Edit billing or profile information.</li>
                             <li>Invite others to their account.</li>
                             <li>View their messages.</li>
                        </ul>
                        
                        @endif
                        
                        @if($requestRole == "Administrative Support")
                        
                        <ul style="list-style-type: none; font-size: 14px;">
                             <li>Edit Cases.</li>
                             <li>Create new cases on their behalf.</li>
                             <li>Edit billing or profile information.</li>
                             <li>Invite others to their account.</li>
                             <li>View their messages.</li>
                        </ul>
                        
                         @endif
                         
                @endif
                 
                <p>
                    @if($role == "office")
                    
                    Login and visit the Shared Users section to approve or cancel request.
                    
                    @elseif($role == "admin")
                    
                    Login and visit the Shared Users section to approve or cancel request.
                    
                    @else
                    
                    Login and visit the AGENCIES section to view their cases.
                    
                    @endif
                    
                </p>
              </td>
            </tr>
            <!-- end copy -->

            <!-- start button -->
            <tr>
              <td align="left" bgcolor="#ffffff">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                    <td align="center" bgcolor="#ffffff" style="padding: 12px">
                      <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                          <td
                            align="center"
                            bgcolor="#1a82e2"
                            style="border-radius: 6px"
                          >
                            <a
                              href="https://engagedlearning.net/"
                              target="_blank"
                              style="
                                display: inline-block;
                                padding: 16px 36px;
                                font-family: 'Source Sans Pro', Helvetica, Arial,
                                  sans-serif;
                                font-size: 16px;
                                color: #ffffff;
                                text-decoration: none;
                                border-radius: 6px;
                                background: linear-gradient(
                                  219.34deg,
                                  #0570e0,
                                  #022181
                                );
                              "
                              >Login</a
                            >
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <!-- end button -->

            <!-- start copy -->
            <tr>
              <td
                align="left"
                bgcolor="#ffffff"
                style="
                  padding: 24px;
                  font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif;
                  font-size: 16px;
                  line-height: 24px;
                "
              >
                <p style="margin: 0">
                  Thank you for your continued collaboration and trust in our platform.
                </p>
              </td>
            </tr>
            <!-- end copy -->
          </table>
          <!--[if (gte mso 9)|(IE)]>
        </td>
        </tr>
        </table>
        <![endif]-->
        </td>
      </tr>
      <!-- end copy block -->

      <!-- start footer -->
      <tr>
        <td align="center" bgcolor="#e9ecef" style="padding: 24px">
          <!--[if (gte mso 9)|(IE)]>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
        <tr>
        <td align="center" valign="top" width="600">
        <![endif]-->
          <table
            border="0"
            cellpadding="0"
            cellspacing="0"
            width="100%"
            style="max-width: 600px"
          >
            <!-- start permission -->
            <tr>
              <td
                align="center"
                bgcolor="#e9ecef"
                style="
                  padding: 12px 24px;
                  font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif;
                  font-size: 14px;
                  line-height: 20px;
                  color: #666;
                "
              >
                <p style="margin: 0">
                  Should you have any queries or require further assistance regarding this shared access, please don't hesitate to reach out. Our team is dedicated to ensuring a smooth and productive experience for all users.
                </p>
              </td>
            </tr>
            <!-- end permission -->
          </table>
          <!--[if (gte mso 9)|(IE)]>
        </td>
        </tr>
        </table>
        <![endif]-->
        </td>
      </tr>
      <!-- end footer -->
    </table>
    <!-- end body -->
  </body>
</html>
