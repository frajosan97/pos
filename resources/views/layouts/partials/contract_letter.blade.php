<!-- Page 1 -->
<div class="page">
    <h1 class="title-1">CARDINAL EMPIRE LIMITED</h1>
    <h2 class="title-2"><u>CONTRACT OF EMPLOYMENT</u></h2>

    <p>This Agreement made as of the <b class="inline-text">{!! formatDateWithOrdinal() !!}</b> and is in line with the provisions of the Employment Act, 2007, Laws of Kenya.</p>

    <h2 class="title-3">BETWEEN:</h2>
    <p><strong>CARDINAL EMPIRE LTD</strong>, a limited liability company incorporated in the Republic of Kenya and whose address is P.O. Box 45152-00100, Nairobi (hereinafter referred to as “<b>the Employer</b>”).</p>
    <p><strong>OF THE FIRST PART</strong></p>

    <h2 class="title-3">AND:</h2>
    <p class="mb-5"><b class="inline-text">{{ strtoupper($employee->name) }}</b> of Identification Number <b class="inline-text">{{ $employee->id_number }}</b><br>(Hereinafter referred to as “<b>the Employee</b>”).</p>

    <p class="mb-5"><strong>WHEREAS</strong> The Employee and the Employer wish to enter into an employment agreement governing the terms and conditions of employment.</p>

    <p><strong>THIS AGREEMENT WITNESSES</strong> That in consideration of the promises and mutual covenants and agreement hereinafter contained and for other good and valuable consideration (the receipt and sufficiency of which is hereby acknowledged by the parties hereto), it is agreed by and between the parties hereto to the following terms and conditions:</p>
</div>

<!-- Page 2 -->
<div class="page">
    <h2 class="title-4">A. DEFINITIONS</h2>
    <p>In this Contract, unless the context otherwise requires:</p>
    <ol>
        <li><strong>“Appointment”</strong> means the term of the contract of service hereby created, the duration of which is ascertainable in accordance with this Contract.</li>
        <li><strong>“Basic Salary”</strong> means the amount specified as such in the current Remuneration Contract.</li>
        <li><strong>“Commencement Date”</strong> means the date the contract takes effect.</li>
        <li><strong>“Party”</strong> means either the Employer or the Employee.</li>
        <li><strong>“Parties”</strong> means both the Employer and the Employee.</li>
    </ol>

    <h2 class="title-4">B. TERMS OF EMPLOYMENT</h2>
    <p>The employment of the Employee shall commence on the <b class="inline-text">{!! formatDateWithOrdinal($employee->signed_at) !!}</b> and continue until terminated in accordance with the provisions of this employment agreement. It is a <strong>Two [2] Years</strong> contract. Upon completion, the contract between the Employer and Employee can be renewed.</p>

    <h2 class="title-4">C. DUTIES OF THE EMPLOYEE</h2>
    <p>The Employee will work as a Salesperson and Customer Care Attendant at Kamakis Safaricom Shop - Customer Care but can be subject to a transfer based on the Decision of the Employer.</p>
    <p>These duties and responsibilities may be amended from time to time at the sole discretion of the Employer, subject to formal notification of same being provided to the Employee.</p>

    <div class="ms-2">
        <h2 class="title-4">1. HOURS OF WORK</h2>
        <p>The hours of work are:</p>
        <ul>
            <li><strong>8.00 am - 8.00 pm:</strong></li>
            <li><strong>Public Holidays:</strong> The Company may require you to work on a public holiday due to the nature of the Company’s business. Public Holidays shall be those as may be gazetted by the Government of Kenya.</li>
        </ul>
        <p>In the event the Employer requires the Employee to work in another shop or location, the place of work shall be the place the Employee has been assigned to perform his duties by the Employer. The Hours of work shall be the hours of work at the Place of work.</p>
    </div>
</div>

<!-- Page 3 -->
<div class="page">
    <div class="ms-2">
        <h2 class="title-4">2. COMPENSATION</h2>
        <p><strong>2.1 Salary</strong></p>
        <p>In consideration of the services provided by the Employee, during the term of his employment, his basic salary at the commencement of his employment shall be <strong>Ksh 15,000 (Gross)</strong> a month and will be subject to all the statutory deductions in force. The Employee will receive on-target allowances of <strong>Ksh 5,000</strong>.</p>

        <h2 class="title-4">3. TERMINATION OF EMPLOYMENT</h2>
        <p>The Employer may terminate the employment of the Employee at any time by:</p>
        <p><strong>3.1 Mutual Agreement</strong></p>
        <p>The Employee may terminate this contract by giving the employer a <strong>two (2) months’ notice</strong> in writing. If the Employee does not honor this, they will lose the salary of that month and any pending payments from the Employer.</p>
        <p><strong>3.2 Discharge for Cause</strong></p>
        <p>The Employee may terminate the employment due to a valid reason by giving <strong>two (2) months’ written notice</strong> or salary in lieu of such notice. Subject to this, any dues and/or loans taken from the Company must be settled before resignation.</p>
        <p>In addition, the Employer may terminate this employment by way of summary dismissal due to any of the reasons listed herein below:</p>
        <ol>
            <li>The Employee commits a material breach of his obligations and duties, willfully disobeys lawful orders and instructions of the Company or is absent from his duties without leave; or</li>
            <li>The Employee becomes intoxicated at work; or</li>
            <li>Insubordination by the Employee; or</li>
            <li>The Employee becomes abusive and/or violent to the employer or other employees; or</li>
            <li>Dishonesty that amounts to gross misconduct as decided by the employer; or</li>
            <li>Gross misconduct of duty by the Employee; or</li>
            <li>Fraud, misrepresentation, or other acts of criminal conduct by the Employee; or</li>
            <li>The Employee shall become bankrupt or be guilty of any grave or willful misconduct which, in the opinion of the Employer, has injured or is likely to injure the Employer or its business; or</li>
            <li>A medical officer of or acting on behalf of the Employer should verify that the Employee is, by reason of ill health or injury, incapable of rendering further satisfactory services to the Employer; or</li>
            <li>A material breach by the Employee of any term of this agreement.</li>
        </ol>
        <p>The Employer shall observe all company procedures as outlined in the Company handbook prior to summarily dismissing an Employee.</p>
        <h2 class="title-4">4. LEAVE</h2>
        <p><strong>4.1 Sick Leave</strong></p>
        <p>The Employee who falls sick and is unable to carry out his duties by reason of illness for which he is not in any way at fault shall be entitled after three consecutive months of service with the Company, to sick leave of <strong>seven (7) days with full pay</strong>, and thereafter to sick leave of <strong>seven (7) days with half pay</strong> in each period of twelve months of consecutive service, subject to production of a certificate of incapacity to work signed by a duly registered medical practitioner. An employee is not entitled to claim for any extra.</p>

        <p><strong>4.2 Notification of Sickness</strong></p>
        <p>Should the Employee be in a position where he is unable to attend work due to sickness and his absence has not previously been duly authorized, he must inform his manager of the fact of his absence and the full reasons for it as soon as is reasonably practical. The Employee shall be required to provide a medical certificate proving the reason for his absence.</p>
        <p>Immediately following the Employee’s return to work after a period of absence which has not previously been authorized by his manager, he is required to complete a self-certification form stating the dates of and the reason for his absence, including details of sickness on non-working days as this information is required by the company for calculating statutory sick pay entitlement. Self-certification forms will be retained in the company's records.</p>

        <p><strong>4.3 Notification of Any Other Absence</strong></p>
        <p>Should the Employee be in a position where he is unable to attend work for any reason and his absence has not previously been duly authorized, he must inform his manager of the fact of his absence and the full reasons for it as soon as is reasonably practical.</p>
        <p>Immediately following the Employee’s return to work after a period of absence which has not previously been authorized by his manager, he is required to complete a self-certification form stating the dates of and the reason for his absence as this information is required by the Company for calculating annual leave balance. Self-certification forms will be retained in the Company's records.</p>
    </div>
</div>

<!-- Page 4 -->
<div class="page">
    <div class="ms-2">
        <h2 class="title-4">5. OTHER EMPLOYMENT</h2>
        <p>The Employee must devote the whole of his time, attention, and abilities during the hours of work for Cardinal Empire Limited to his duties for the company. He may not, under any circumstances, whether directly or indirectly, undertake any other duties, of whatever kind, during the hours of work for the company.</p>
        <p>The Employee may not, without the prior written consent of his manager, which will not be unreasonably withheld, engage, whether directly or indirectly, in any business or employment which is similar or in any way connected to or competitive with the business of Cardinal Empire Limited or which could or might reasonably be considered by others to impair his ability to act at all times in the best interest of the company outside the hours of work for the company.</p>

        <h2 class="title-4">6. NON-COMPETE CLAUSE</h2>
        <p>The Employee acknowledges that the Company is in a highly competitive industry and that his leaving the Company to join a competing business would jeopardize the Company’s customers, confidences, confidential information, and customer relationships. Accordingly, the Employee agrees, subject to the provisions below, including those relating to payments that may be due to the Employee, that during his employment with the Company, and for a period of <strong>six (6) months</strong> after the date of termination of employment for any reason (the “Non-Compete Period”), the employee will not directly or indirectly own, invest in, render any services or provide advice to, or act as officer, director, employee, or independent employee for, any person or entity that competes with the Company in the products or services it offers to its clients including any subsidiary or related company or related business of such person or entity. The Employee acknowledges that given the nature of the business of the Company and the geographical market of the Company combined with his role and responsibilities, the period of six (6) months is reasonable.</p>
        <p>The employee should not work for any other company that offers the same services or any other services and products Cardinal Empire Limited offers or shall not, within a period of <strong>one [1] year</strong> upon termination of the contract, join another company that offers the same services.</p>
        <p>The services include:</p>
        <ul>
            <li>Lipa na Mpesa and buy goods and pay bill</li>
            <li>ICT solutions e.g., Firewall security, cloud backup, Telematics</li>
            <li>Phone and accessory</li>
            <li>FTT [fibre to the home and fibre to the business]</li>
        </ul>
        <p>And all other products and services released or introduced by Cardinal Empire Limited and Safaricom PLC in the market.</p>
    </div>
</div>

<!-- Page 5 -->
<div class="page">
    <div class="ms-2">
        <h2 class="title-4">7. NON-SOLICITATION CLAUSE</h2>
        <p>The Employee also agrees that for the <strong>one (1) year</strong> period following the termination of employment for whatever reason, the Employee will not directly or indirectly (i.e., through a third party, such as a colleague or recruiter) solicit, recruit, hire or seek to hire (whether on his own behalf or on behalf of some other person or entity) any person who is at that time (or was during the prior six (6) months) an employee, consultant, or independent staff of the Company.</p>

        <h2 class="title-4">8. CONFIDENTIALITY</h2>
        <p>It is recorded that for the purpose of this agreement, confidential information shall mean and include: any information of whatever nature [including WhatsApp, email, signal], which has been or may be obtained by the Employee from the Employer, whether in writing or in electronic form or pursuant to discussions between the Parties, or which can be obtained by examination, testing, visual inspection or analysis, including, without limitation to, scientific, business or financial data, know-how, formulae, processes, designs, sketches, photographs, plans, drawings, specifications, sample reports, models, customer lists, price lists, studies, findings, computer software/s, inventions or ideas, concepts, compilations, studies and other material prepared by or in possession or control of the recipient which contain or otherwise reflect or are generated from any such information as is specified in this definition.</p>
        <p>Each Employee shall hold in confidence all confidential information received from the Employer, customers, and Client/s, and shall not divulge the confidential information to any person, including any other employee/s, except for those employees who are directly involved with the execution of that relevant agreement.</p>
        <p>Each Employee shall return to the company upon request and, in any event, upon the termination of his employment, all documents and tangible items which belong to the company or which contain or refer to any confidential information and which are in his possession or under his control.</p>
        <p>The Employee shall, if requested by the company, delete all confidential information from any re-usable material and destroy all other documents and tangible items which contain or refer to any confidential information and which are in his possession or under his control.</p>
    </div>
</div>

<!-- Page 6 -->
<div class="page">
    <div class="ms-2">
        <h2 class="title-4">9. LIABILITY</h2>
        <p>Should the Employee’s conduct, actions, or failure to act lead to any loss to Cardinal Empire Limited, monetary or otherwise, the Employee shall bear the liability and the means to rectify the loss to the extent of indemnifying Cardinal Empire Limited.</p>
        <p>It is recorded that for the purpose of this agreement, conduct shall include sabotage, conspiracy, gross negligence, fraud, and/or unlawful conduct; among others.</p>
        <h2 class="title-4">10. EQUIPMENT AND RESOURCES PROVIDED TO THE EMPLOYEE</h2>
        <p>The Employee must make his best efforts to look after all documents, equipment, and materials he may receive from the Company, and to preserve them in good condition.</p>
        <p>The use of these tools shall be solely and exclusively for corporate and professional purposes, as a work tool or instrument for the performance of the duties inherent to his work post.</p>
        <p>Notwithstanding the above, it shall be considered acceptable for the Employee to use the said tools for personal purposes, provided that such use is minimal and to the extent strictly necessary. In any event, the Employee is not permitted to view, download, send or receive illegal material or to save on the Company’s computer files that are not related to his employment activities.</p>

        <h2 class="title-4">11. SEVERABILITY</h2>
        <p>Each paragraph of this agreement shall be and remain separate from and independent of and severable from all and any other paragraphs herein except where otherwise indicated by the context of the agreement. The decision or declaration that one or more of the paragraphs are null and void under any enactment or rule of law shall have no effect on the remaining paragraphs of this agreement and it is hereby agreed and understood that each party to this contract is fully aware of his or her rights under the Employment Act in place for the time being.</p>

        <h2 class="title-4">12. NOTICE</h2>
        <p>Any notice required to be given hereunder shall be deemed to have been properly given if delivered personally or sent by prepaid registered mail as follows:</p>
        <ul>
            <li>to the Employee: of P.O. Box Number <u>…………………..</u>, Nairobi, Kenya</li>
            <li>to the Employer: P.O. Box 45152-00100, Nairobi, Kenya.</li>
        </ul>
        <p>Either party may change its address for notice at any time, by giving notice in writing to the other party pursuant to the provisions of this agreement.</p>

        <h2 class="title-4">13. MISCELLANEOUS</h2>
        <p>The Employee shall be required to comply with all Company policies, ethics, and rules and regulations.</p>
        <p>The Contract may only be amended or modified by virtue of a written document signed by or on behalf of the Parties.</p>
        <p>The Contract constitutes the entire agreement between the Parties in relation to its content and revokes and replaces any previous contracts or agreements of any type, whether verbal or written, in relation to its content between the Parties or previous job offer.</p>
    </div>
</div>

<!-- Page 7 -->
<div class="page">
    <div class="ms-2">
        <h2 class="title-4">14. DISPUTE RESOLUTION</h2>
        <p>The parties shall attempt to resolve any disputes arising or relating to this contract through negotiations with senior members of Management who have authority to do so.</p>
        <p>If the Dispute is not resolved after 30 days of the written Invitation to negotiate, the parties will attempt to resolve the dispute in good faith through an agreed Alternative Dispute Resolution (ADR) procedure.</p>
        <p>Nothing in this clause shall be construed as prohibiting a party or its affiliate from applying to a court for interim relief.</p>

        <h2 class="title-4">15. INTERPRETATION OF AGREEMENT</h2>
        <p>The validity, interpretation, construction, and performance of this agreement shall be governed by the Laws of Kenya. This agreement shall be interpreted with all necessary changes as the context may require and shall ensure to be to the benefit of and be binding upon the respective successors and assigns of the parties hereto.</p>
        <p>This Contract shall be read and interpreted together with the Employee Handbook provisions.</p>
    </div>

    <div class="signature-section">
        <p><strong>IN WITNESS WHEREOF</strong> The parties hereto have caused this agreement to be executed as of the
            <b class="inline-text">
                {!! formatDateWithOrdinal($employee->signed_at,'day') !!}
            </b> Day of
            <b class="inline-text">
                {!! formatDateWithOrdinal($employee->signed_at,'month') !!}
            </b>,
            {{ formatDateWithOrdinal($employee->signed_at,'year') }}.
        </p>
    </div>
</div>