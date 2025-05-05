import mysql.connector
from reportlab.platypus import (Paragraph, SimpleDocTemplate, Spacer, Image, 
                               Table, TableStyle, PageBreak, Frame, 
                               PageTemplate, BaseDocTemplate)
from reportlab.lib.pagesizes import A4, inch
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib import colors
from reportlab.lib.utils import ImageReader
import matplotlib.pyplot as plt
from io import BytesIO
import os
from datetime import datetime
from database_connection import get_db_connection

# ========== CONFIGURATION ==========
# Brand colors
WHITE = "#FFFFFF"
PRIMARY_COLOR = "#1e52a5"
SECONDARY_COLOR = "#003f91"
ACCENT_COLOR = "#112f5e"
HIGHLIGHT_COLOR = "#DBAE58"
LIGHT_BG = "#F5F8FA"

# Paths
LOGO_PATH = '/Applications/MAMP/htdocs/2024-25_GP_02/WebsitePages/images/Logo2.png'
REPORTS_DIR = 'reports'
TOP_LETTERHEAD = "/Applications/MAMP/htdocs/2024-25_GP_02/WebsitePages/images/report_top.png"  # 1414×211 pixels
BOTTOM_LETTERHEAD = "/Applications/MAMP/htdocs/2024-25_GP_02/WebsitePages/images/report_bottom.png"  # 1414×230 pixels

# Chart dimensions
HALF_WIDTH = 3.0  # inches
HALF_HEIGHT = 2.4 
FULL_WIDTH = 6.0     
FULL_HEIGHT = 3.0

# Letterhead dimensions (converted from pixels at 300dpi)
TOP_LETTERHEAD_HEIGHT = 230 / 300 * inch  # ~0.703 inches
BOTTOM_LETTERHEAD_HEIGHT = 230 / 300 * inch  # ~0.767 inches
LETTERHEAD_WIDTH = 1414 / 300 * inch  # ~4.713 inches

# Set matplotlib style
plt.style.use('ggplot')  # Default clean style
plt.switch_backend('Agg')

def generate_pdf_report(event_id):
    """Generate PDF report with letterhead integration"""
    #print(f"Generating PDF report for event ID: {event_id}")
    
    # Database connection
    connection = get_db_connection()
    cursor = connection.cursor(dictionary=True)
    cursor.execute("SELECT * FROM events WHERE EventID = %s", (event_id,))
    event = cursor.fetchone()
    
    if not event:
        cursor.close()
        connection.close()
        raise ValueError(f"Event with ID {event_id} not found.")

    report_path = f'{REPORTS_DIR}/event_{event_id}_report.pdf'
    if os.path.exists(report_path):
        print(f"Report already exists: {report_path}")
        return report_path

    report_data = fetch_report_data(cursor, event_id)
    charts = generate_charts(report_data)
    cursor.close()
    connection.close()

    # ===== PDF Setup with Letterhead =====
    doc = BaseDocTemplate(
        report_path,
        pagesize=A4,
        #leftMargin=0.75*inch,
        rightMargin=0.75*inch,
        topMargin=TOP_LETTERHEAD_HEIGHT + 0.25*inch,
        bottomMargin=BOTTOM_LETTERHEAD_HEIGHT
    )
    
    from PIL import Image as PILImage
    top_img = PILImage.open(TOP_LETTERHEAD)
    bottom_img = PILImage.open(BOTTOM_LETTERHEAD)
    
    # 2. Calculate exact scaling (maintain original aspect ratio)
    A4_WIDTH, A4_HEIGHT = A4
    TOP_SCALE = A4_WIDTH / top_img.width
    BOTTOM_SCALE = A4_WIDTH / bottom_img.width
    TOP_HEIGHT = top_img.height * TOP_SCALE
    BOTTOM_HEIGHT = bottom_img.height * BOTTOM_SCALE

    # 3. PDF Setup with precise letterhead spacing
    doc = BaseDocTemplate(
        report_path,
        pagesize=A4,
        leftMargin=0,  # Full bleed for letterhead
        rightMargin=0,
        topMargin=TOP_HEIGHT + 0.2*inch,  # Space for top letterhead
        bottomMargin=BOTTOM_HEIGHT + 0.2*inch  # Space for bottom
    )

    def load_transparent_letterhead(path, width):
        """Load image with transparency handling"""
        from PIL import Image as PILImage
        img = PILImage.open(path)
        
        # Convert to RGBA if not already
        if img.mode != 'RGBA':
            img = img.convert('RGBA')
            
        # Save as temporary PNG with transparency
        temp_path = os.path.join(os.path.dirname(path), 'temp_transparent.png')
        img.save(temp_path, format='PNG')
        
        return Image(temp_path, 
                   width=width,
                   height=(img.height/img.width)*width,
                   kind='proportional')

    # Load letterheads with transparency
    letterhead_top = load_transparent_letterhead(TOP_LETTERHEAD, A4_WIDTH)
    letterhead_bottom = load_transparent_letterhead(BOTTOM_LETTERHEAD, A4_WIDTH)

    # 5. Content frame with standard margins
    content_frame = Frame(
        0.75*inch,  # Left margin
        doc.bottomMargin + 0.1*inch,  # Bottom margin
        A4_WIDTH - 1.5*inch,  # Width accounting for margins
        A4_HEIGHT - doc.topMargin - doc.bottomMargin - 0.3*inch,
        showBoundary=0  # Set to 1 to debug frame boundaries
    )

    # 6. Precise drawing function for Canva design
    def draw_canva_letterhead(canvas, doc):
        canvas.saveState()
        # Draw top letterhead (full width)
        canvas.drawImage(TOP_LETTERHEAD,
                        0,  # X - start at left edge
                        A4_HEIGHT - TOP_HEIGHT,  # Y - from top
                        width=A4_WIDTH,
                        height=TOP_HEIGHT,
                        preserveAspectRatio=True,
                        anchor='n',
                        mask='auto')
        # Draw bottom letterhead (full width)
        canvas.drawImage(BOTTOM_LETTERHEAD,
                        0,  # X - start at left edge
                        0,  # Y - at bottom
                        width=A4_WIDTH,
                        height=BOTTOM_HEIGHT,
                        preserveAspectRatio=True,
                        anchor='s',
                        mask='auto')
        canvas.restoreState()

    doc.addPageTemplates([
        PageTemplate(
            id='CanvaDesign',
            frames=content_frame,
            onPage=draw_canva_letterhead
        )
    ])

    # 7. Build story with proper spacing
    story = [
        *build_document_story(event, report_data, charts, create_styles()),
    ]

    doc.build(story)
    return report_path


def create_styles():
    """Create and return paragraph styles for the document."""
    styles = getSampleStyleSheet()
    
    styles.add(ParagraphStyle(
        name='TitleStyle', 
        fontName='Helvetica-Bold', 
        fontSize=22, 
        spaceAfter=14, 
        textColor=PRIMARY_COLOR,
        alignment=1  # Centered
    ))
    
    styles.add(ParagraphStyle(
        name='SubtitleStyle', 
        fontName='Helvetica', 
        fontSize=12, 
        spaceAfter=18, 
        textColor=SECONDARY_COLOR,
        alignment=1
    ))
    
    styles.add(ParagraphStyle(
        name='SectionHeaderStyle', 
        fontName='Helvetica-Bold', 
        fontSize=16, 
        spaceAfter=12, 
        textColor=SECONDARY_COLOR,
        backColor=LIGHT_BG,
        borderPadding=(6, 6, 6, 6),
        leading=18
    ))
    
    styles.add(ParagraphStyle(
        name='HighlightStyle', 
        fontName='Helvetica-Bold', 
        fontSize=18, 
        spaceAfter=12, 
        textColor=HIGHLIGHT_COLOR,
        alignment=1
    ))
    
    styles.add(ParagraphStyle(
        name='NormalStyle', 
        fontName='Helvetica', 
        fontSize=12, 
        spaceAfter=12, 
        textColor=ACCENT_COLOR,
        leading=14
    ))
    
    styles.add(ParagraphStyle(
        name='BulletStyle', 
        fontName='Helvetica', 
        fontSize=12, 
        spaceAfter=6, 
        textColor=ACCENT_COLOR,
        leftIndent=12,
        bulletIndent=0,
        bulletFontName='Helvetica-Bold',
        bulletFontSize=12
    ))
    
    return styles

def fetch_report_data(cursor, event_id):
    """Fetch all data needed for the report from the database."""
    data = {}

    # Number of halls
    cursor.execute("""
        SELECT COUNT(*) AS numOfhalls
        FROM hall
        WHERE EventID = %s
    """, (event_id,))
    data['number_of_halls'] = cursor.fetchone()['numOfhalls']

    
    # Total attendance
    cursor.execute("""
        SELECT COUNT(DISTINCT pt.ID) AS total
        FROM persontrack pt
        JOIN monitoredsession ms ON pt.SessionID = ms.SessionID
        JOIN hall h ON ms.HallID = h.HallID
        WHERE h.EventID = %s;
    """, (event_id,))
    data['total_attendance'] = cursor.fetchone()['total']


    # Gender distribution
    cursor.execute("""
        SELECT Gender, COUNT(DISTINCT pt.ID) AS count
        FROM persontrack pt
        JOIN monitoredsession ms ON pt.SessionID = ms.SessionID
        JOIN hall h ON ms.HallID = h.HallID
        WHERE h.EventID = %s
        GROUP BY pt.Gender;
    """, (event_id,))
    data['gender_data'] = cursor.fetchall()

    # Hall distribution
    cursor.execute("""
        SELECT h.HallName, COUNT(*) AS count
        FROM persontrack pt
        JOIN monitoredsession ms ON pt.SessionID = ms.SessionID
        JOIN hall h ON ms.HallID = h.HallID
        WHERE h.EventID = %s
        GROUP BY h.HallName;
    """, (event_id,))
    data['hall_data'] = cursor.fetchall()

    # Hourly attendance
    cursor.execute("""
        SELECT HOUR(pt.EntranceTime) AS hour, COUNT(*) AS count
        FROM persontrack pt
        JOIN monitoredsession ms ON pt.SessionID = ms.SessionID
        JOIN hall h ON ms.HallID = h.HallID
        WHERE h.EventID = %s
        GROUP BY hour
        ORDER BY hour;
    """, (event_id,))
    data['hourly_data'] = cursor.fetchall()

    # Daily attendance
    cursor.execute("""
        SELECT DATE(pt.EntranceTime) AS date, COUNT(*) AS count
        FROM persontrack pt
        JOIN monitoredsession ms ON pt.SessionID = ms.SessionID
        JOIN hall h ON ms.HallID = h.HallID
        WHERE h.EventID = %s
        GROUP BY date
        ORDER BY date;
    """, (event_id,))
    daily_data = cursor.fetchall()
    data['daily_data'] = daily_data

        # Find most popular day
    if daily_data:
        popular_day = max(daily_data, key=lambda x: x['count'])
        data['popular_day'] = {
            'date': popular_day['date'].strftime('%Y-%m-%d'),
            'count': popular_day['count']
        }
    else:
        data['popular_day'] = {'date': "N/A", 'count': 0}
    
    # Average visit duration
    cursor.execute("""
        SELECT AVG(TIMESTAMPDIFF(MINUTE, pt.EntranceTime, pt.ExitTime)) AS avg_duration
        FROM persontrack pt
        JOIN monitoredsession ms ON pt.SessionID = ms.SessionID
        JOIN hall h ON ms.HallID = h.HallID
        WHERE h.EventID = %s AND pt.ExitTime IS NOT NULL;
    """, (event_id,))
    avg_duration = cursor.fetchone()['avg_duration']
    data['avg_duration'] = f"{int(avg_duration // 60)}h {int(avg_duration % 60)}m" if avg_duration else "N/A"


    # Calculate average visits per person
    cursor.execute("""
        SELECT AVG(visit_count) as avg_visits 
        FROM (SELECT ID, COUNT(*) as visit_count FROM persontrack WHERE SessionID IN 
            (SELECT SessionID FROM monitoredsession WHERE HallID IN 
            (SELECT HallID FROM hall WHERE EventID = %s)) 
            GROUP BY ID) as visits
    """, (event_id,))
    data['avg_visits'] = cursor.fetchone()['avg_visits']
    
    # Peak hour
    if data['hourly_data']:
        peak_hour_data = max(data['hourly_data'], key=lambda x: x['count'])
        data['peak_hour'] = f"{peak_hour_data['hour']}:00 - {peak_hour_data['hour']+1}:00 ({peak_hour_data['count']} visitors)"
    else:
        data['peak_hour'] = "N/A"
    
    # Most popular hall
    if data['hall_data']:
        popular_hall = max(data['hall_data'], key=lambda x: x['count'])
        data['popular_hall'] = f"{popular_hall['HallName']} ({popular_hall['count']} visitors)"
    else:
        data['popular_hall'] = "N/A"
    
    return data

def generate_charts(report_data):
    """Generate all charts for the report with consistent sizing."""
    charts = {}
    
    # Make all charts half-width for side-by-side display
    charts['gender_chart'] = create_pie_chart(
        report_data['gender_data'], 
        "Gender Distribution",
        colors=[PRIMARY_COLOR, HIGHLIGHT_COLOR, SECONDARY_COLOR],
        width=HALF_WIDTH,
        height=HALF_HEIGHT
    )
    
    charts['hall_chart'] = create_bar_chart(
        report_data['hall_data'], 
        "Visitors per Hall",
        color=PRIMARY_COLOR,
        width=HALF_WIDTH,
        height=HALF_HEIGHT
    )
    
    # Update time charts to be same size
    charts['hourly_chart'] = create_line_chart(
        report_data['hourly_data'], 
        "Hourly Attendance",
        color=SECONDARY_COLOR,
        width=HALF_WIDTH,
        height=HALF_HEIGHT
    )
    
    charts['daily_chart'] = create_bar_chart(
        report_data['daily_data'], 
        "Daily Attendance",
        color=HIGHLIGHT_COLOR,
        x_label_key='date',
        width=HALF_WIDTH,
        height=HALF_HEIGHT
    )
    
    return charts

def build_document_story(event, report_data, charts, styles):
    """Build the document content structure with professional layout."""
    story = []
    
    # ===== Title Section =====
    story.append(Paragraph(f"<font color='{HIGHLIGHT_COLOR}'>{event['EventName']}</font> EVENT REPORT", 
                         styles['TitleStyle']))
    story.append(Spacer(1, 0.1*inch))
    
    # Horizontal line separator
    story.append(Spacer(1, 0.05*inch))
    story.append(Table([[""]], colWidths=[6.5*inch], style=[
        ('LINEABOVE', (0,0), (0,0), 1, colors.HexColor(PRIMARY_COLOR))
    ]))
    story.append(Spacer(1, 0.1*inch))



    start_time = format_timedelta(event['EventStartTime'])
    end_time = format_timedelta(event['EventEndTime'])
    
    
    # ===== Event Details - Side by Side Layout =====
    event_details = Table([
        [
            # Left column - event info
            Table([
                [Paragraph("<b>Event Start:</b>", styles['NormalStyle']),
                 Paragraph(f"{event['EventStartDate']} at {start_time}", styles['NormalStyle'])],
                [Paragraph("<b>Event End:</b>", styles['NormalStyle']),
                 Paragraph(f"{event['EventEndDate']} at {end_time}", styles['NormalStyle'])]
            ], colWidths=[1.2*inch, 3*inch], style=[
                ('VALIGN', (0,0), (-1,-1), 'TOP'),
                ('LEFTPADDING', (0,0), (-1,-1), 0),
                ('RIGHTPADDING', (0,0), (-1,-1), 0),
                ('BOTTOMPADDING', (0,0), (0,-1), 8)
            ]),
            
            # Right column - 
            Table([
                [Paragraph("<b>Event Location:</b>", styles['NormalStyle']),
                 Paragraph(event['EventLocation'], styles['NormalStyle'])],
                 [Paragraph("<b>Number of halls:</b>", styles['NormalStyle']),
                 Paragraph(f"{report_data['number_of_halls']}", styles['NormalStyle'])]
            ], colWidths=[1.8*inch, 1*inch], style=[
                ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
                ('LEFTPADDING', (0,0), (-1,-1), 8),
                ('RIGHTPADDING', (0,0), (-1,-1), 8),
                ('BOTTOMPADDING', (0,0), (0,-1), 8)
            ])
        ]
    ], colWidths=[4.2*inch, 2.8*inch])
    
    story.append(event_details)
    story.append(Spacer(1, 0.2*inch))
    
    # ===== Key Metrics - Card Layout =====

        # Update metrics_data to include popular day
    metrics_data = [
        ("Total Attendance", str(report_data['total_attendance'])), 
        ("Average Visit Duration", report_data['avg_duration']),
        ("Peak Hour", report_data['peak_hour'].split(' (')[0]),
        ("Most Popular Hall", report_data['popular_hall'].split(' (')[0]),
        ("Busiest Day", f"{report_data['popular_day']['date']} ({report_data['popular_day']['count']} visitors)")
    ]
    
    # Create metric cards with alternating colors
    metric_colors = [PRIMARY_COLOR, SECONDARY_COLOR, HIGHLIGHT_COLOR, ACCENT_COLOR, PRIMARY_COLOR]

    metric_cards = []
    for (title, value), color in zip(metrics_data, metric_colors):
        card = Table([
            [Paragraph(title, ParagraphStyle(
                name='MetricTitle',
                parent=styles['NormalStyle'],
                textColor=color,
                fontSize=11,
                alignment=1,
                spaceBefore=0,  # Ensure no additional spacing
                spaceAfter=0
            ))],
            [Spacer(1, 2)],  # Minimal vertical spacer
            [Paragraph(value, ParagraphStyle(
                name='MetricValue',
                parent=styles['NormalStyle'],
                textColor=ACCENT_COLOR,
                fontSize=12,
                fontName='Helvetica-Bold',
                alignment=1,
                spaceBefore=0,
                spaceAfter=0
            ))]
        ], style=[
            ('BACKGROUND', (0,0), (-1,-1), LIGHT_BG),
            ('BOX', (0,0), (-1,-1), 0.5, colors.lightgrey),
            ('ROUNDEDCORNERS', [4,4,4,4]),
            ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
            ('ALIGN', (0,0), (-1,-1), 'CENTER'),
            ('LEFTPADDING', (0,0), (-1,-1), 0),  # Zero left padding
            ('RIGHTPADDING', (0,0), (-1,-1), 0), # Zero right padding
            ('TOPPADDING', (0,0), (-1,-1), 4),   # Small top padding only
            ('BOTTOMPADDING', (0,0), (-1,-1), 4) # Small bottom padding only
        ])
        metric_cards.append(card)

    # Arrange with minimal spacing
    metrics_grid = Table([
        [metric_cards[0], Spacer(0.05*inch, 0), metric_cards[1], Spacer(0.05*inch, 0), metric_cards[2]],
        [Spacer(1, 0.05*inch)],
        [metric_cards[3], Spacer(0.05*inch, 0), metric_cards[4]]
    ], colWidths=[
        2.8*inch,    # Wider columns to use space
        0.05*inch,   # Tiny spacer
        2.8*inch,
        0.05*inch,
        2.8*inch
    ], style=[
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('LEFTPADDING', (0,0), (-1,-1), 0),
        ('RIGHTPADDING', (0,0), (-1,-1), 0)
    ])

    story.append(metrics_grid)
    story.append(Spacer(1, 0.3*inch))  # Reduced final spacer
    
    # ===== Charts Section =====
    story.append(Paragraph("ATTENDANCE ANALYSIS", 
                         ParagraphStyle(name='SectionHeaderAllCaps',
                                       parent=styles['SectionHeaderStyle'],
                                       fontSize=13,
                                       textTransform='uppercase',
                                       spaceAfter=0.1*inch)))
    
    # Gender and Hall charts with descriptions
    chart_row = Table([
        [
            # Left column - gender chart
            Table([
                [Image(charts['gender_chart'], width=HALF_WIDTH*inch, height=HALF_HEIGHT*inch)],
                [Spacer(1, 0.1*inch)],
                [Paragraph("<b>Gender Distribution</b>", styles['NormalStyle'])],
                [Paragraph("Understanding attendee demographics helps tailor marketing strategies.", 
                          ParagraphStyle(name='ChartCaption', parent=styles['NormalStyle'], fontSize=8))]
            ], style=[
                ('VALIGN', (0,0), (-1,-1), 'TOP'),
                ('ALIGN', (0,0), (-1,-1), 'CENTER')
            ]),
            
            # Right column - hall chart
            Table([
                [Image(charts['hall_chart'], width=HALF_WIDTH*inch, height=HALF_HEIGHT*inch)],
                [Spacer(1, 0.1*inch)],
                [Paragraph("<b>Hall Popularity</b>", styles['NormalStyle'])],
                [Paragraph("Shows which content resonated most with attendees.", 
                          ParagraphStyle(name='ChartCaption', parent=styles['NormalStyle'], fontSize=8))]
            ], style=[
                ('VALIGN', (0,0), (-1,-1), 'TOP'),
                ('ALIGN', (0,0), (-1,-1), 'CENTER')
            ])
        ]
    ], colWidths=[HALF_WIDTH*inch, HALF_WIDTH*inch])
    
    story.append(chart_row)
    story.append(Spacer(1, 0.5*inch))
    
    # ===== Time-Based Charts =====
    story.append(Paragraph("ATTENDANCE PATTERNS", 
                         ParagraphStyle(name='SectionHeaderAllCaps',
                                      parent=styles['SectionHeaderStyle'],
                                      fontSize=14,
                                      textTransform='uppercase',
                                      spaceAfter=0.3*inch)))
    
    # Time charts row
    time_chart_row = Table([
        [
            # Left column - hourly chart
            Table([
                [Image(charts['hourly_chart'], width=HALF_WIDTH*inch, height=HALF_HEIGHT*inch)],
                [Spacer(1, 0.1*inch)],
                [Paragraph("<b>Hourly Attendance</b>", styles['NormalStyle'])],
                [Paragraph("Peak times help optimize staff scheduling and session timing.", 
                         ParagraphStyle(name='ChartCaption', parent=styles['NormalStyle'], fontSize=10))]
            ], style=[
                ('VALIGN', (0,0), (-1,-1), 'TOP'),
                ('ALIGN', (0,0), (-1,-1), 'CENTER')
            ]),
            
            # Right column - daily chart
            Table([
                [Image(charts['daily_chart'], width=HALF_WIDTH*inch, height=HALF_HEIGHT*inch)],
                [Spacer(1, 0.1*inch)],
                [Paragraph("<b>Daily Attendance</b>", styles['NormalStyle'])],
                [Paragraph(f"Peak day: {report_data['popular_day']['date']} ({report_data['popular_day']['count']} visitors)", 
                         ParagraphStyle(name='ChartCaption', parent=styles['NormalStyle'], fontSize=10))]
            ], style=[
                ('VALIGN', (0,0), (-1,-1), 'TOP'),
                ('ALIGN', (0,0), (-1,-1), 'CENTER')
            ])
        ]
    ], colWidths=[HALF_WIDTH*inch, HALF_WIDTH*inch])
    
    story.append(time_chart_row)
    story.append(Spacer(1, 0.5*inch))
    
    story.append(PageBreak())
    
    # ===== Insights Section =====
    story.append(Paragraph("STRATEGIC INSIGHTS", 
                         ParagraphStyle(name='SectionHeaderAllCaps',
                                       parent=styles['SectionHeaderStyle'],
                                       fontSize=14,
                                       textTransform='uppercase',
                                       spaceAfter=0.3*inch)))
    
    insights_header = Paragraph(
        "Based on the attendance data analysis, here are key recommendations:",
        ParagraphStyle(name='InsightHeader', parent=styles['NormalStyle'], spaceAfter=0.2*inch)
    )
    story.append(insights_header)
    
    insights = [
        {
            "title": "Peak Day Analysis",
            "content": f"The busiest day was {report_data['popular_day']['date']} with {report_data['popular_day']['count']} visitors. Consider what made this day particularly successful."
        },
        {
            "title": "Peak Hours Management",
            "content": f"Consider adding more staff during {report_data['peak_hour'].split(' (')[0]} to improve visitor experience."
        },
        {
            "title": "Hall Utilization",
            "content": f"The most popular hall was {report_data['popular_hall'].split(' (')[0]}. Consider expanding similar content."
        },
        {
            "title": "Visitor Engagement",
            "content": f"Attendees visited an average of {report_data['avg_visits']:.0f} different halls - higher repeat visits indicate good content distribution."
        },
        {
            "title": "Underutilized Spaces",
            "content": "Analyze halls with lower attendance to understand if content or location was the issue."
        },
        {
            "title": "Timing Optimization",
            "content": f"With average visit duration of {report_data['avg_duration']}, adjust session lengths accordingly."
        },
        {
            "title": "Gender Balance",
            "content": "Review marketing strategies if gender distribution doesn't match target demographics."
        },
        {
            "title": "Daily Trends",
            "content": "Use daily attendance patterns to optimize resource allocation."
        }
    ]
    
    for insight in insights:
        # Insight title with colored bullet
        story.append(Paragraph(
            f"<font color='{PRIMARY_COLOR}'>•</font> <b>{insight['title']}</b>",
            ParagraphStyle(name='InsightTitle', parent=styles['NormalStyle'], 
                          leftIndent=10, spaceAfter=4, textColor=ACCENT_COLOR)
        ))
        # Insight content
        story.append(Paragraph(
            insight['content'],
            ParagraphStyle(name='InsightContent', parent=styles['NormalStyle'], 
                          leftIndent=24, spaceAfter=12, fontSize=11)
        ))
    
    # Conclusion with colored border
    conclusion_table = Table([
        [Paragraph(
            "This report provides actionable insights to enhance future events. " +
            "For more detailed analysis or custom reporting, please contact our analytics team.",
            ParagraphStyle(name='ConclusionStyle', parent=styles['NormalStyle'],
                          textColor=SECONDARY_COLOR, fontSize=11, alignment=1)
    )]
    ], style=[
        ('BOX', (0,0), (0,0), 1, colors.HexColor(HIGHLIGHT_COLOR)),
        ('PADDING', (0,0), (0,0), 12),
        ('BACKGROUND', (0,0), (0,0), LIGHT_BG)
    ])
    
    story.append(Spacer(1, 0.3*inch))
    story.append(conclusion_table)
    
    return story


def create_pie_chart(data, title, colors=None, width=HALF_WIDTH, height=HALF_HEIGHT):
    """Create a pie chart with the given data."""
    labels = [d['Gender'] for d in data]
    sizes = [d['count'] for d in data]
    
    fig, ax = plt.subplots(figsize=(width, height))
    fig.patch.set_facecolor(LIGHT_BG)
    fig.patch.set_alpha(0.7)
    
    if colors and len(colors) >= len(labels):
        wedges, texts, autotexts = ax.pie(
            sizes, 
            labels=labels, 
            autopct='%1.1f%%', 
            startangle=90,
            colors=colors,
            textprops={'fontsize': 9}
        )
    else:
        wedges, texts, autotexts = ax.pie(
            sizes, 
            labels=labels, 
            autopct='%1.1f%%', 
            startangle=90,
            textprops={'fontsize': 9}
        )
    
    ax.axis('equal')  
    plt.title(title, pad=15, fontsize=11, fontweight='bold')
    plt.setp(autotexts, size=9, weight="bold")
    plt.setp(texts, size=9)
    plt.tight_layout()
    
    return save_chart_to_image(fig)

# def create_bar_chart(data, title, color=None, x_label_key='HallName', width=HALF_WIDTH, height=HALF_HEIGHT):
#     """Create a bar chart with the given data."""
#     fig, ax = plt.subplots(figsize=(width, height))
#     labels = [d[x_label_key] for d in data]
#     counts = [d['count'] for d in data]
    
#     fig.patch.set_facecolor(LIGHT_BG)
#     fig.patch.set_alpha(0.7)
    
#     bar_color = color if color else PRIMARY_COLOR
#     bars = ax.bar(labels, counts, color=bar_color)
    
#     for bar in bars:
#         height = bar.get_height()
#         ax.text(bar.get_x() + bar.get_width()/2., height,
#                 f'{height}',
#                 ha='center', va='bottom', fontsize=8)
    
#     ax.set_title(title, pad=12, fontsize=11, fontweight='bold')
#     ax.set_ylabel('Visitors', labelpad=8, fontsize=9)
#     plt.xticks(rotation=45, ha='right', fontsize=8)
#     plt.yticks(fontsize=8)
    
#     ax.spines['top'].set_visible(False)
#     ax.spines['right'].set_visible(False)
#     ax.grid(axis='y', linestyle='--', alpha=0.5)
#     plt.tight_layout()
    
#     return save_chart_to_image(fig)

def create_line_chart(data, title, color=None, width=HALF_WIDTH, height=HALF_HEIGHT):
    """Create a line chart optimized for side-by-side display."""
    x_values = [f"{int(d['hour']):02d}:00" for d in data]
    y_values = [d['count'] for d in data]
    
    fig, ax = plt.subplots(figsize=(width, height))
    fig.patch.set_facecolor(LIGHT_BG)
    fig.patch.set_alpha(0.7)
    
    line_color = color if color else PRIMARY_COLOR
    line = ax.plot(x_values, y_values, marker='o', color=line_color, linewidth=2, markersize=5)
    
    # Only label every other point to reduce clutter
    for i, (x, y) in enumerate(zip(x_values, y_values)):
        if i % 2 == 0:  # Label every other hour
            ax.text(x, y, f'{y}', ha='center', va='bottom', fontsize=8)
    
    ax.set_title(title, pad=10, fontsize=11, fontweight='bold')
    ax.set_xlabel('Time', labelpad=6, fontsize=9)
    ax.set_ylabel('Visitors', labelpad=6, fontsize=9)
    plt.xticks(rotation=45, ha='right', fontsize=8)
    plt.yticks(fontsize=8)
    
    ax.spines['top'].set_visible(False)
    ax.spines['right'].set_visible(False)
    ax.grid(axis='y', linestyle='--', alpha=0.5)
    plt.tight_layout()
    
    return save_chart_to_image(fig)

def create_bar_chart(data, title, color=None, x_label_key='HallName', width=HALF_WIDTH, height=HALF_HEIGHT):
    """Create a bar chart optimized for side-by-side display."""
    labels = [str(d[x_label_key]) for d in data]
    counts = [d['count'] for d in data]
    
    fig, ax = plt.subplots(figsize=(width, height))
    fig.patch.set_facecolor(LIGHT_BG)
    fig.patch.set_alpha(0.7)
    
    bar_color = color if color else PRIMARY_COLOR
    
    # Highlight max value for daily chart
    if x_label_key == 'date' and data:
        max_idx = counts.index(max(counts))
        colors = [HIGHLIGHT_COLOR if i == max_idx else bar_color for i in range(len(counts))]
    else:
        colors = bar_color
    
    bars = ax.bar(labels, counts, color=colors, width=0.6)  # Narrower bars
    
    # Only label bars above certain height to reduce clutter
    max_count = max(counts) if counts else 0
    for bar in bars:
        height = bar.get_height()
        if height > max_count * 0.2:  # Only label significant bars
            ax.text(bar.get_x() + bar.get_width()/2., height,
                   f'{int(height)}',
                   ha='center', va='bottom', fontsize=8)
    
    ax.set_title(title, pad=10, fontsize=11, fontweight='bold')
    ax.set_ylabel('Visitors', labelpad=6, fontsize=9)
    plt.xticks(rotation=45, ha='right', fontsize=8)
    ax.yaxis.set_major_locator(plt.MaxNLocator(integer=True))
    plt.yticks(fontsize=8)
    
    ax.spines['top'].set_visible(False)
    ax.spines['right'].set_visible(False)
    ax.grid(axis='y', linestyle='--', alpha=0.5)
    plt.tight_layout()
    
    return save_chart_to_image(fig)

def save_chart_to_image(fig):
    """Save matplotlib figure to an in-memory PNG file."""
    buf = BytesIO()
    fig.savefig(buf, format='PNG', dpi=150, bbox_inches='tight', transparent=True)
    plt.close(fig)
    buf.seek(0)
    return buf


# reformate end time
def format_timedelta(tdelta):
    total_seconds = int(tdelta.total_seconds())
    hours, remainder = divmod(total_seconds, 3600)
    minutes, _ = divmod(remainder, 60)
    return f"{hours:02d}:{minutes:02d}"
    