<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	version="1.0">
	<xsl:output method="html" />

	<xsl:variable name="project.name"
		select="/info/property[@name='projectname']/@value" />
	<xsl:variable name="currentlog"
		select="substring(cruisecontrol/info/property[@name='logfile']/@value, 0, string-length(cruisecontrol/info/property[@name='logfile']/@value) - 3)" />

	<xsl:template match="checkstyle" mode="phpcs-list">
		<p />
		<table class="result">
			<colgroup>
				<col width="1%"></col>
				<col width="90%"></col>
				<col width="9%"></col>
			</colgroup>
			<thead>
				<tr>
					<th colspan="2">PHP CodeSniffer Violation in Files</th>
					<th>Errors / Warnings</th>
				</tr>
			</thead>
			<tbody>
				<xsl:for-each select="/checkstyle/file[error]">
					<xsl:sort select="@name" />
					<xsl:variable name="errors"
						select="/checkstyle/file[@name=current()/@name]/error" />
					<xsl:variable name="errorCount" select="count($errors[@severity='error'])" />
					<xsl:variable name="warningCount"
						select="count($errors[@severity='warning'])" />
					<tr>
						<xsl:if test="position() mod 2 = 1">
							<xsl:attribute name="class">oddrow</xsl:attribute>
						</xsl:if>
						<td colspan="2">
							<xsl:attribute name="class">
                    <xsl:choose>
                      <xsl:when test="$errorCount &gt; 0">
                        <xsl:text>error</xsl:text>
                      </xsl:when>
                      <xsl:otherwise>
                        <xsl:text>warning</xsl:text>
                      </xsl:otherwise>
                    </xsl:choose>
                  </xsl:attribute>
							<a class="stealth" href="?log={$currentlog}&amp;tab=phpcs#a{position()}">
								<xsl:value-of select="@name" />
							</a>
						</td>
						<td align="right">
							<xsl:value-of select="$errorCount" />
							/
							<xsl:value-of select="$warningCount" />
						</td>
					</tr>
				</xsl:for-each>
			</tbody>
			<tfoot>
				<tr>
					<td></td>
					<td align="right">
						Files:
						<xsl:value-of select="count(file[error])" />
					</td>
					<td align="right">
						<xsl:value-of select="count(file/error[@severity='error'])" />
						/
						<xsl:value-of select="count(file/error[@severity='warning'])" />
					</td>
				</tr>
			</tfoot>
		</table>
	</xsl:template>

</xsl:stylesheet>